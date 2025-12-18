<?php

namespace app\admin\controller;

use app\admin\model\Apply as ApplyModel;
use app\Request;
use think\facade\Queue;

class Apply
{
    public function __construct(
        private ApplyModel $apply,
        private Request $request
    ) {}

    /**
     * 订单列表页面
     */
    public function index(): \think\response\View
    {
        return view('apply/index');
    }

    /**
     * 获取订单列表数据
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
        $system_order = $searchParams['system_order'] ?? $params['system_order'] ?? '';
        $merchant_order = $searchParams['merchant_order'] ?? $params['merchant_order'] ?? '';
        $address = $searchParams['address'] ?? $params['address'] ?? '';
        $status = $searchParams['status'] ?? $params['status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($system_order) {
            $where[] = ['system_order', 'like', '%' . $system_order . '%'];
        }
        if ($merchant_order) {
            $where[] = ['merchant_order', 'like', '%' . $merchant_order . '%'];
        }
        if ($address) {
            $where[] = ['address', 'like', '%' . $address . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        $list = $this->apply->where($where)
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

    /**
     * 重新处理订单（状态为-1时）
     */
    public function retryProcess(): \think\response\Json
    {
        $id = $this->request->param('id');
        
        if (!$id) {
            return json(['code' => -1, 'msg' => '订单ID不能为空']);
        }

        $order = $this->apply->find($id);
        if (!$order) {
            return json(['code' => -1, 'msg' => '订单不存在']);
        }

        if ($order->status != -1) {
            return json(['code' => -1, 'msg' => '当前订单状态不允许重新处理']);
        }

        Queue::push('\app\task\callback\Apply', $order, 'ApplyTransfer');
        $order->status = 0;
        $order->save();
        return json(['code' => 1, 'msg' => '重新处理成功']);
    }


    /**
     * 重发通知（状态为3时）
     */
    public function resendNotify(): \think\response\Json
    {
        $id = $this->request->param('id');
        
        if (!$id) {
            return json(['code' => -1, 'msg' => '订单ID不能为空']);
        }

        $order = $this->apply->find($id);
        if (!$order) {
            return json(['code' => -1, 'msg' => '订单不存在']);
        }

        if ($order->status != 3) {
            return json(['code' => -1, 'msg' => '当前订单状态不允许重发通知']);
        }

        Queue::push('\app\task\callback\Pay', $order->toArray(), 'Pay');

        $order->save(['status' => 2]);

        return json(['code' => 1, 'msg' => '重发通知成功']);
    }
}

