<?php

namespace app\admin\controller;

use app\admin\model\SendDetail as SendDetailModel;
use app\Request;

class SendDetail
{
    public function __construct(
        private SendDetailModel $sendDetail,
        private Request $request
    ) {}

    /**
     * 下发明细列表页面
     */
    public function index(): \think\response\View
    {
        return view('send_detail/index');
    }

    /**
     * 获取下发明细列表数据
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
        $wallet_address = $searchParams['wallet_address'] ?? $params['wallet_address'] ?? '';
        $to_address = $searchParams['to_address'] ?? $params['to_address'] ?? '';
        $txid = $searchParams['txid'] ?? $params['txid'] ?? '';
        $status = $searchParams['status'] ?? $params['status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($wallet_address) {
            $where[] = ['wallet_address', 'like', '%' . $wallet_address . '%'];
        }
        if ($to_address) {
            $where[] = ['to_address', 'like', '%' . $to_address . '%'];
        }
        if ($txid) {
            $where[] = ['txid', 'like', '%' . $txid . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        $list = $this->sendDetail->where($where)
            ->order('id', 'desc')
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
}


