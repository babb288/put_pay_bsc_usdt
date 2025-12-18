<?php /*a:1:{s:72:"C:\Users\bbab\PhpstormProjects\binance0\app\admin\view\report\index.html";i:1766031731;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>统计报表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/lib/layui-v2.6.3/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/css/public.css" media="all">
    <style>
        .report-card-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .report-card-sub {
            color: #999;
            margin-top: 4px;
            font-size: 12px;
        }
        .report-section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">

        <div class="layui-row layui-col-space15">
            <!-- 商户与钱包概览 -->
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header report-section-title">商户与钱包概览</div>
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space15">
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['merchant_total']) && ($stats['merchant_total'] !== '')?$stats['merchant_total']:0)); ?></div>
                                        <div class="report-card-sub">商户总数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['merchant_active']) && ($stats['merchant_active'] !== '')?$stats['merchant_active']:0)); ?></div>
                                        <div class="report-card-sub">启用商户数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['wallet_put_count']) && ($stats['wallet_put_count'] !== '')?$stats['wallet_put_count']:0)); ?></div>
                                        <div class="report-card-sub">代收钱包数量</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['wallet_pay_count']) && ($stats['wallet_pay_count'] !== '')?$stats['wallet_pay_count']:0)); ?></div>
                                        <div class="report-card-sub">代付钱包数量</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['wallet_bnb_total']) && ($stats['wallet_bnb_total'] !== '')?$stats['wallet_bnb_total']:'0.00000000')); ?></div>
                                        <div class="report-card-sub">平台BNB总余额</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['wallet_usdt_total']) && ($stats['wallet_usdt_total'] !== '')?$stats['wallet_usdt_total']:'0.00000000')); ?></div>
                                        <div class="report-card-sub">平台USDT总余额</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs12">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['merchant_balance_all']) && ($stats['merchant_balance_all'] !== '')?$stats['merchant_balance_all']:'0.00')); ?></div>
                                        <div class="report-card-sub">商户余额总额</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 今日订单概览 -->
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header report-section-title">
                        今日订单概览
                        <span class="report-card-sub" style="margin-left: 10px;">
                            <?php echo htmlentities((string) (isset($stats['today_start']) && ($stats['today_start'] !== '')?$stats['today_start']:'')); ?> ~ <?php echo htmlentities((string) (isset($stats['today_end']) && ($stats['today_end'] !== '')?$stats['today_end']:'')); ?>
                        </span>
                    </div>
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space15">
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['apply_today_count']) && ($stats['apply_today_count'] !== '')?$stats['apply_today_count']:0)); ?></div>
                                        <div class="report-card-sub">今日代付订单数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['apply_today_amount']) && ($stats['apply_today_amount'] !== '')?$stats['apply_today_amount']:'0.00')); ?></div>
                                        <div class="report-card-sub">今日代付金额 (USDT)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['order_today_count']) && ($stats['order_today_count'] !== '')?$stats['order_today_count']:0)); ?></div>
                                        <div class="report-card-sub">今日代收订单数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['order_today_amount']) && ($stats['order_today_amount'] !== '')?$stats['order_today_amount']:'0.00')); ?></div>
                                        <div class="report-card-sub">今日代收金额 (USDT)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs12">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['send_success_total']) && ($stats['send_success_total'] !== '')?$stats['send_success_total']:'0.00')); ?></div>
                                        <div class="report-card-sub">累计下发成功金额 (USDT)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="layui-row layui-col-space15" style="margin-top: 10px;">
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['apply_total_count']) && ($stats['apply_total_count'] !== '')?$stats['apply_total_count']:0)); ?></div>
                                        <div class="report-card-sub">代付订单总数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['apply_total_amount']) && ($stats['apply_total_amount'] !== '')?$stats['apply_total_amount']:'0.00')); ?></div>
                                        <div class="report-card-sub">代付累计金额 (USDT)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['order_total_count']) && ($stats['order_total_count'] !== '')?$stats['order_total_count']:0)); ?></div>
                                        <div class="report-card-sub">代收订单总数</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs6">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['order_total_amount']) && ($stats['order_total_amount'] !== '')?$stats['order_total_amount']:'0.00')); ?></div>
                                        <div class="report-card-sub">代收累计金额 (USDT)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-row layui-col-space15" style="margin-top: 15px;">
            <!-- 归集任务统计 -->
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header report-section-title">归集任务统计</div>
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space15">
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['collect_running']) && ($stats['collect_running'] !== '')?$stats['collect_running']:0)); ?></div>
                                        <div class="report-card-sub">进行中</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['collect_success']) && ($stats['collect_success'] !== '')?$stats['collect_success']:0)); ?></div>
                                        <div class="report-card-sub">成功</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['collect_failed']) && ($stats['collect_failed'] !== '')?$stats['collect_failed']:0)); ?></div>
                                        <div class="report-card-sub">失败</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 授权任务统计 -->
            <div class="layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header report-section-title">授权任务统计</div>
                    <div class="layui-card-body">
                        <div class="layui-row layui-col-space15">
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['auth_running']) && ($stats['auth_running'] !== '')?$stats['auth_running']:0)); ?></div>
                                        <div class="report-card-sub">进行中/待处理</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['auth_success']) && ($stats['auth_success'] !== '')?$stats['auth_success']:0)); ?></div>
                                        <div class="report-card-sub">已完成</div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-xs4">
                                <div class="layui-card">
                                    <div class="layui-card-body">
                                        <div class="report-card-number"><?php echo htmlentities((string) (isset($stats['auth_failed']) && ($stats['auth_failed'] !== '')?$stats['auth_failed']:0)); ?></div>
                                        <div class="report-card-sub">失败</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>


