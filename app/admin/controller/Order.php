<?php

namespace app\admin\controller;

use app\admin\model\Order as OrderModel;
use app\Request;

class Order
{
    public function __construct(
        private OrderModel $order,
        private Request $request
    ) {}

    /**
     * 代收订单列表页面
     */
    public function index(): \think\response\View
    {
        return view('order/index');
    }

    /**
     * 获取代收订单列表数据
     */
    public function list(): \think\response\Json
    {
        $params = $this->request->param();

        $page  = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 15;

        // 处理搜索参数（支持 searchParams JSON 字符串格式）
        $searchParams = [];
        if (isset($params['searchParams']) && !empty($params['searchParams'])) {
            $searchParams = json_decode($params['searchParams'], true) ?: [];
        }

        // 兼容直接传参的方式
        $username     = $searchParams['username'] ?? $params['username'] ?? '';
        $system_order = $searchParams['system_order'] ?? $params['system_order'] ?? '';
        $address      = $searchParams['address'] ?? $params['address'] ?? '';
        $status       = $searchParams['status'] ?? $params['status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($system_order) {
            $where[] = ['system_order', 'like', '%' . $system_order . '%'];
        }
        if ($address) {
            $where[] = ['address', 'like', '%' . $address . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        $list = $this->order->where($where)
            ->order('id', 'desc')
            ->paginate([
                'list_rows' => $limit,
                'page'      => $page,
            ]);

        return json([
            'code'  => 0,
            'msg'   => 'success',
            'count' => $list->total(),
            'data'  => $list->items()
        ]);
    }
}


