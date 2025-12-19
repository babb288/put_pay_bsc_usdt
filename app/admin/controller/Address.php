<?php

namespace app\admin\controller;

use app\admin\model\Address as AddressModel;
use app\admin\model\AuthorizationTask;
use app\admin\model\Merchant as MerchantModel;
use app\Bsc;
use app\Request;

class Address
{
    public function __construct(
        private AddressModel $address,
        private Request $request,
        private Bsc $bsc,
        private AuthorizationTask $task,
        private MerchantModel $merchant
    ) {}

    /**
     * 地址列表页面
     */
    public function index(): \think\response\View
    {
        return view('address/index');
    }

    /**
     * 获取地址列表数据
     */
    public function list(): \think\response\Json
    {
        $params = $this->request->param();
        
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 15;
        
        // 处理搜索参数（支持 searchParams JSON 字符串格式）
        $searchParams = [];
        if (isset($params['searchParams']) && !empty($params['searchParams'])) {
            $searchParams = json_decode($params['searchParams'], true) ?: [];
        }
        
        // 兼容直接传参的方式
        $username = $searchParams['username'] ?? $params['username'] ?? '';
        $address = $searchParams['address'] ?? $params['address'] ?? '';
        $bind_data = $searchParams['bind_data'] ?? $params['bind_data'] ?? '';
        $status = $searchParams['status'] ?? $params['status'] ?? '';
        $isAuthorized = $searchParams['is_authorized'] ?? $params['is_authorized'] ?? '';
        $order = ['id' => 'desc'];

        if(isset($searchParams['sort'])){
            $order = ['balance' => $searchParams['sort']];
        }

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($address) {
            $where[] = ['address', 'like', '%' . $address . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        if ($isAuthorized !== '') {
            $where[] = ['is_authorized', '=', $isAuthorized];
        }

        if ($bind_data !== '') {
            $where[] = ['bind_data', '=', $bind_data];
        }



        $list = $this->address->where($where)
            ->order($order)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ]);

        return json([
            'code' => 0,
            'msg' => 'success',
            'count' => $list->total(),
            'data' => $list->items()
        ]);
    }

    /**
     * 更新状态
     */
    public function updateStatus(): \think\response\Json
    {
        $id = $this->request->param('id', 0);
        $status = $this->request->param('status', -1);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        if (!in_array($status, [0, 1])) {
            return json(['code' => -1, 'msg' => '状态值不正确']);
        }

        $address = $this->address->find($id);
        if (!$address) {
            return json(['code' => -1, 'msg' => '地址不存在']);
        }

        $address->status = $status;
        $result = $address->save();

        if ($result) {
            return json(['code' => 1, 'msg' => '状态更新成功']);
        } else {
            return json(['code' => -1, 'msg' => '状态更新失败']);
        }
    }

    /**
     * 刷新余额
     */
    public function refreshBalance(): \think\response\Json
    {
        $id = $this->request->param('id', 0);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $address = $this->address->find($id);
        if (!$address) {
            return json(['code' => -1, 'msg' => '地址不存在']);
        }

        $balance = $this->bsc->getTokenBalance(tokenContract: '0x55d398326f99059ff775485246999027b3197955',address: $address->address);

        $bnb_balance= $this->bsc->getBnbBalance(address: $address->address);

        $address->save([
            'balance'       => $balance,
            'bnb_balance'   => $bnb_balance,
        ]);
        return json(['code' => 1, 'msg' => '刷新成功']);
    }

    /**
     * 获取商户列表（用于下拉选择）
     */
    public function getMerchantList(): \think\response\Json
    {
        $merchants = $this->merchant->where('status', 1)
            ->field('id,username')
            ->order('id', 'desc')
            ->select();
        
        return json([
            'code' => 1,
            'msg' => 'success',
            'data' => $merchants
        ]);
    }

    /**
     * 添加检测授权任务
     */
    public function addAuthTask(): \think\response\Json
    {
        $username = $this->request->param('username', '');
        
        if (empty($username)) {
            return json(['code' => -1, 'msg' => '请选择商户']);
        }

        $addresses = $this->address
            ->where('username', $username)
            ->where('balance','>',0)
            ->where('status', 1)

            ->select();
        
        // 统计未授权和已授权数量
        $unauthorizedCount = 0;
        $authorizedCount = 0;

        $totalCount = count($addresses);
        
        foreach ($addresses as $address) {
            if ($address->is_authorized == 1) {
                $authorizedCount++;
            } else {
                $unauthorizedCount++;
            }
        }

        // 检查任务是否已存在
        $existingTask = $this->task->where('username', $username)->where('task_status',0)->find();

        if ($existingTask) {
            return json(array('code' => -1,'msg' => '当前存在任务'));
        } else {
            $result = $this->task->create([
                'username'              => $username,
                'unauthorized_count'    => $unauthorizedCount,
                'authorized_count'      => $authorizedCount,
                'total_count'           => $totalCount,
                'task_status'           => 0,
            ]);
        }
        
        if ($result) {
            return json(['code' => 1, 'msg' => '添加检测授权任务成功']);
        } else {
            return json(['code' => -1, 'msg' => '添加任务失败']);
        }
    }
}


