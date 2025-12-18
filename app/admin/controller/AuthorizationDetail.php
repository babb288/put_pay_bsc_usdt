<?php

namespace app\admin\controller;

use app\admin\model\AuthorizationDetail as AuthorizationDetailModel;
use app\Request;

class AuthorizationDetail
{
    public function __construct(
        private AuthorizationDetailModel $authorizationDetail,
        private Request $request
    ) {}

    /**
     * 授权明细列表页面
     */
    public function index(): \think\response\View
    {
        return view('authorization_detail/index');
    }

    /**
     * 获取授权明细列表数据
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
        $is_bnb = $searchParams['is_bnb'] ?? $params['is_bnb'] ?? '';
        $is_approve = $searchParams['is_approve'] ?? $params['is_approve'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($address) {
            $where[] = ['address', 'like', '%' . $address . '%'];
        }
        if ($is_bnb !== '') {
            $where[] = ['is_bnb', '=', $is_bnb];
        }
        if ($is_approve !== '') {
            $where[] = ['is_approve', '=', $is_approve];
        }

        $list = $this->authorizationDetail->where($where)
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

