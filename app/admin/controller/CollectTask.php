<?php

namespace app\admin\controller;

use app\admin\model\CollectTask as CollectTaskModel;
use app\admin\model\Merchant as MerchantModel;
use app\Bsc;
use app\Request;
use think\facade\Db;

class CollectTask
{
    public function __construct(
        private CollectTaskModel $collectTask,
        private Request $request,
        private Bsc $bsc,
        private MerchantModel $merchant,
    ) {}

    /**
     * 归集任务列表页面
     */
    public function index(): \think\response\View
    {
        return view('collect_task/index');
    }

    /**
     * 获取归集任务列表数据
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
        $status = $searchParams['status'] ?? $params['status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        $list = $this->collectTask->where($where)
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
     * 刷新归集状态
     */
    public function refreshStatus(): \think\response\Json
    {
        $id = $this->request->param('id', 0);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $task = $this->collectTask->find($id);
        if (!$task) {
            return json(['code' => -1, 'msg' => '任务不存在']);
        }

        if (empty($task->collect_hash)) {
            return json(['code' => -1, 'msg' => '该任务暂无归集哈希，无法刷新状态']);
        }

        // 只有进行中的任务可以刷新
        if ((int)$task->status !== 1 or (int)$task->status !== 0) {
            return json(['code' => -1, 'msg' => '只有进行中的任务可以刷新状态']);
        }

        // 查询链上交易回执
        $result = $this->bsc->getTransactionReceipt($task->collect_hash);

        // null：交易仍在 pending 或查询异常
        if ($result === null) {
            return json(['code' => 1, 'msg' => '交易未确认，请稍后再试']);
        }
        
        // false：交易失败
        if ($result === false) {
            // 归还服务费到商户余额（按用户名匹配）
            if ($task->contract_fee > 0) {
                $this->merchant
                    ->where('username', $task->username)
                    ->update(['balance' => Db::raw('+'.$task->contract_fee)]);
            }

            $task->status = 3; // 失败
            $task->save();
            return json(['code' => 1, 'msg' => '归集交易失败，服务费已退回，状态已更新为失败']);
        }

        // true：交易成功
        $task->status = 2; // 成功
        $task->save();

        return json(['code' => 1, 'msg' => '归集交易成功，状态已更新为成功']);
    }
}

