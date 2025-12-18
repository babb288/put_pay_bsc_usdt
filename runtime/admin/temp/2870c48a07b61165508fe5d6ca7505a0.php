<?php /*a:1:{s:78:"C:\Users\bbab\PhpstormProjects\binance0\app\admin\view\collect_task\index.html";i:1766029712;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>归集任务列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/lib/layui-v2.6.3/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/css/public.css" media="all">
    <style type="text/css">
        .table-header-fixed {
            position: fixed;
            top: 0;
            z-index: 99
        }

        .layui-table-cell {
            line-height: 24px !important;
            vertical-align: middle !important;
            height: auto !important;
            overflow: visible !important;
            text-overflow: inherit !important;
            white-space: normal !important;
            word-break: break-all !important;
            word-wrap: break-word !important;
            padding: 8px 15px !important;
            max-width: none !important;
        }

        .layui-table td, .layui-table th {
            padding: 0 !important;
        }

        .layui-table-body .layui-table-cell {
            min-height: 40px;
        }

        .layui-table-view .layui-table-body td {
            white-space: normal !important;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">

        <fieldset class="table-search-fieldset">
            <legend></legend>
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">商户用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="username" placeholder="请输入商户用户名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-inline">
                                <select name="status" lay-filter="status">
                                    <option value="">全部</option>
                                    <option value="0">待处理</option>
                                    <option value="1">进行中</option>
                                    <option value="2">成功</option>
                                    <option value="3">失败</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit lay-filter="data-search-btn"><i class="layui-icon">&#xe615;</i> 搜 索</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>

        <script type="text/html" id="toolbarDemo">
            <!-- 工具栏为空 -->
        </script>

        <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

        <script type="text/html" id="statusTpl">
            {{# if(d.status == 0){ }}
                <span class="layui-badge layui-bg-gray">待处理</span>
            {{# } else if(d.status == 1){ }}
                <span class="layui-badge layui-bg-blue">进行中</span>
            {{# } else if(d.status == 2){ }}
                <span class="layui-badge layui-bg-green">成功</span>
            {{# } else if(d.status == 3){ }}
                <span class="layui-badge layui-bg-red">失败</span>
            {{# } else { }}
                <span class="layui-badge layui-bg-gray">未知</span>
            {{# } }}
        </script>

        <script type="text/html" id="currentTableBar">
            {{# if(d.status == 1){ }}
                <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="refresh">刷新状态</a>
            {{# } }}
        </script>

    </div>
</div>
<script src="/static/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>

<script>
    layui.use(['form', 'table', 'layer', 'jquery'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            layer = layui.layer;

        table.render({
            elem: '#currentTableId',
            url: '/collect_task/list',
            toolbar: '#toolbarDemo',
            cellMinWidth: 80,
            defaultToolbar: ['filter', 'exports', 'print'],
            cols: [[
                {field: 'id', title: 'ID', width: 80, sort: true, fixed: 'left', align: 'center'},
                {field: 'username', title: '商户用户名', minWidth: 150, align: 'center'},
                {field: 'wallet_count', title: '钱包总数量', width: 120, align: 'center'},
                {field: 'collect_amount', title: '归集金额', width: 150, align: 'center', templet: function(d){
                    if(!d.collect_amount) return '0';
                    var amount = parseFloat(d.collect_amount);
                    if(amount % 1 === 0) {
                        return amount.toString();
                    }
                    return amount.toFixed(2);
                }},
                {field: 'contract_fee', title: '合约服务费', width: 150, align: 'center', templet: function(d){
                    if(!d.contract_fee) return '0';
                    var fee = parseFloat(d.contract_fee);
                    if(fee % 1 === 0) {
                        return fee.toString();
                    }
                    return fee.toFixed(2);
                }},
                {field: 'platform_fee', title: '平台手续费', width: 150, align: 'center', templet: function(d){
                    if(!d.platform_fee) return '0';
                    var fee = parseFloat(d.platform_fee);
                    if(fee % 1 === 0) {
                        return fee.toString();
                    }
                    return fee.toFixed(2);
                }},
                {field: 'collect_hash', title: '归集哈希', minWidth: 250, align: 'center', templet: function(d){
                    if(d.collect_hash) {
                        return '<a href="https://bscscan.com/tx/' + d.collect_hash + '" target="_blank" style="color: #1890ff; word-break: break-all;">' + d.collect_hash + '</a>';
                    }
                    return '-';
                }},
                {field: 'status', title: '状态', width: 120, align: 'center', templet: '#statusTpl'},
                {field: 'create_time', title: '创建时间', width: 180, align: 'center'},
                {field: 'update_time', title: '更新时间', width: 180, align: 'center'},
                {title: '操作', width: 150, templet: '#currentTableBar', fixed: "right", align: "center"}
            ]],
            limits: [10, 20, 30, 50, 100, 500],
            limit: 10,
            page: true,
            done: function (index, layero) {
                $(".layui-table-header tr").resize(function () {
                    $(".layui-table-header tr").each(function (index, val) {
                        $($(".layui-table-fixed .layui-table-header table tr")[index]).height($(val).height());
                    });
                });
                $(".layui-table-header tr").each(function (index, val) {
                    $($(".layui-table-fixed .layui-table-header table tr")[index]).height($(val).height());
                });

                $(".layui-table-body tr").resize(function () {
                    $(".layui-table-body tr").each(function (index, val) {
                        $($(".layui-table-fixed .layui-table-body table tr")[index]).height($(val).height());
                    });
                });
                $(".layui-table-body tr").each(function (index, val) {
                    $($(".layui-table-fixed .layui-table-body table tr")[index]).height($(val).height());
                });
            },
            skin: 'line'
        });

        // 监听搜索操作
        form.on('submit(data-search-btn)', function (data) {
            let result = data.field;
            for (const key in result) {
                if (result[key] === "") {
                    delete result[key];
                }
            }
            result = JSON.stringify(data.field);
            //执行搜索重载
            table.reload('currentTableId', {
                page: {
                    curr: 1
                },
                where: {
                    searchParams: result
                }
            }, 'data');
            return false;
        });

        table.on('tool(currentTableFilter)', function (obj) {
            var data = obj.data;
            
            if (obj.event === 'refresh') {
                var loadIndex = layer.load(1, {shade: [0.5, '#000']});
                $.post('/collect_task/refreshStatus', {id: data.id}, function(res){
                    layer.close(loadIndex);
                    if(res.code == 1){
                        layer.msg('刷新成功', {icon: 1});
                        table.reload('currentTableId');
                    } else {
                        layer.msg(res.msg || '刷新失败', {icon: 2});
                    }
                }).fail(function(){
                    layer.close(loadIndex);
                    layer.msg('网络错误', {icon: 2});
                });
            }
        });

    });
</script>

</body>
</html>

