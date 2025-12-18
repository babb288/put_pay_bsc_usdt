<?php

namespace app\admin\controller;

use app\admin\model\AuthorizationTask as AuthorizationTaskModel;
use app\Request;

class AuthorizationTask
{
    public function __construct(
        private AuthorizationTaskModel $authorizationTask,
        private Request $request
    ) {}

    /**
     * 授权任务列表页面
     */
    public function index(): \think\response\View
    {
        return view('authorization_task/index');
    }

    /**
     * 获取授权任务列表数据
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
        $username    = $searchParams['username'] ?? $params['username'] ?? '';
        $task_status = $searchParams['task_status'] ?? $params['task_status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($task_status !== '') {
            $where[] = ['task_status', '=', $task_status];
        }

        $list = $this->authorizationTask->where($where)
            ->order('id', 'desc')
            ->paginate([
                'list_rows' => $limit,
                'page'      => $page,
            ]);

        return json([
            'code'  => 0,
            'msg'   => 'success',
            'count' => $list->total(),
            'data'  => $list->items(),
        ]);
    }
}


