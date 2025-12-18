<?php

namespace app\admin\controller;

use app\admin\model\Merchant as MerchantModel;
use app\admin\model\Wallet as WalletModel;
use app\admin\model\Apply as ApplyModel;
use app\admin\model\Order as OrderModel;
use app\admin\model\CollectTask as CollectTaskModel;
use app\admin\model\AuthorizationTask as AuthorizationTaskModel;
use app\admin\model\SendDetail as SendDetailModel;
use app\Request;

class Report
{
    public function __construct(
        private MerchantModel $merchant,
        private WalletModel $wallet,
        private ApplyModel $apply,
        private OrderModel $order,
        private CollectTaskModel $collectTask,
        private AuthorizationTaskModel $authorizationTask,
        private SendDetailModel $sendDetail,
        private Request $request,
    ) {}

    /**
     * 统计报表首页
     */
    public function index(): \think\response\View
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        // 商户统计
        $merchantTotal        = $this->merchant->count();
        $merchantActive       = $this->merchant->where('status', 1)->count();
        $merchantBalanceTotal = (float)$this->merchant->sum('balance');

        // 钱包统计
        $walletPutCount = $this->wallet->where('type', 'put')->count();
        $walletPayCount = $this->wallet->where('type', 'pay')->count();
        $walletBnbTotal = (float)$this->wallet->sum('bnb_balance');
        $walletUsdtTotal = (float)$this->wallet->sum('usdt_balance');

        // 代付订单（Apply）统计
        $applyTodayCount   = $this->apply->whereBetweenTime('create_time', $todayStart, $todayEnd)->count();
        $applyTodayAmount  = (float)$this->apply->whereBetweenTime('create_time', $todayStart, $todayEnd)->sum('price');
        $applyTotalCount   = $this->apply->count();
        $applyTotalAmount  = (float)$this->apply->sum('price');

        // 代收订单（Order / pay_order）统计
        $orderTodayCount   = $this->order->whereBetweenTime('create_time', $todayStart, $todayEnd)->count();
        $orderTodayAmount  = (float)$this->order->whereBetweenTime('create_time', $todayStart, $todayEnd)->sum('price');
        $orderTotalCount   = $this->order->count();
        $orderTotalAmount  = (float)$this->order->sum('price');

        // 归集任务统计
        $collectRunning = $this->collectTask->where('status', 1)->count();
        $collectSuccess = $this->collectTask->where('status', 2)->count();
        $collectFailed  = $this->collectTask->where('status', 3)->count();

        // 授权任务统计（0进行中/待处理，1已完成，2失败）
        $authRunning = $this->authorizationTask->where('task_status', 0)->count();
        $authSuccess = $this->authorizationTask->where('task_status', 1)->count();
        $authFailed  = $this->authorizationTask->where('task_status', 2)->count();

        // 下发表统计
        $sendSuccessTotal = (float)$this->sendDetail->where('status', 1)->sum('amount');

        $stats = [
            'merchant_total'       => $merchantTotal,
            'merchant_active'      => $merchantActive,
            'merchant_balance_all' => $merchantBalanceTotal,
            'wallet_put_count'     => $walletPutCount,
            'wallet_pay_count'     => $walletPayCount,
            'wallet_bnb_total'     => $walletBnbTotal,
            'wallet_usdt_total'    => $walletUsdtTotal,
            'apply_today_count'    => $applyTodayCount,
            'apply_today_amount'   => $applyTodayAmount,
            'apply_total_count'    => $applyTotalCount,
            'apply_total_amount'   => $applyTotalAmount,
            'order_today_count'    => $orderTodayCount,
            'order_today_amount'   => $orderTodayAmount,
            'order_total_count'    => $orderTotalCount,
            'order_total_amount'   => $orderTotalAmount,
            'collect_running'      => $collectRunning,
            'collect_success'      => $collectSuccess,
            'collect_failed'       => $collectFailed,
            'auth_running'         => $authRunning,
            'auth_success'         => $authSuccess,
            'auth_failed'          => $authFailed,
            'send_success_total'   => $sendSuccessTotal,
            'today_start'          => $todayStart,
            'today_end'            => $todayEnd,
        ];

        return view('report/index', ['stats' => $stats]);
    }
}


