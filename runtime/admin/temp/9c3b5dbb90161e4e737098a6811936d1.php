<?php /*a:1:{s:74:"C:\Users\bbab\PhpstormProjects\binance0\app\admin\view\merchant\index.html";i:1766029876;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商户列表</title>
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

        /* 确保长文本可以完全显示 */
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
                            <label class="layui-form-label">商户UID</label>
                            <div class="layui-input-inline">
                                <input type="text" name="uid" placeholder="请输入商户UID" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="username" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-inline">
                                <select name="status" lay-filter="type">
                                    <option value="" class="center">全部</option>
                                    <option value="1" class="center">正常</option>
                                    <option value="0" class="center">禁用</option>
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
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-normal layui-btn-sm" lay-event="add">添加商户</button>
            </div>
        </script>

        <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

        <script type="text/html" id="statusTpl">
            {{# if(d.status == 1){ }}
                <span class="layui-badge layui-bg-green">正常</span>
            {{# } else { }}
                <span class="layui-badge layui-bg-gray">禁用</span>
            {{# } }}
        </script>

        <script type="text/html" id="currentTableBar">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="password">修改密码</a>
            {{# if(d.status == 1){ }}
                <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="disable">禁用</a>
            {{# } else { }}
                <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="enable">启用</a>
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
            url: '/merchant/list',
            toolbar: '#toolbarDemo',
            cellMinWidth: 80,
            defaultToolbar: ['filter', 'exports', 'print'],
            cols: [[
                {field: 'id', title: 'ID', width: 80, sort: true, fixed: 'left', align: 'center'},
                {field: 'uid', title: '商户UID', width: 120, align: 'center'},
                {field: 'username', title: '用户名', minWidth: 150, align: 'center'},
                {field: 'address', title: '下发地址', minWidth: 250, align: 'center', templet: function(d){
                    return '<div style="word-break: break-all;">' + (d.address || '-') + '</div>';
                }},
                {field: 'contract_address', title: '合约地址', minWidth: 250, align: 'center', templet: function(d){
                    return '<div style="word-break: break-all;">' + (d.contract_address || '-') + '</div>';
                }},
                {field: 'key', title: '商户密钥', minWidth: 300, align: 'center', templet: function(d){
                    return '<div style="word-break: break-all;">' + (d.key || '-') + '</div>';
                }},
                {field: 'balance', title: '笔数余额', minWidth: 300, align: 'center', templet: function(d){
                        return '<div style="word-break: break-all;">' + (d.balance || '-') + '</div>';
                    }
                },
                {field: 'fee_rate', title: '手续费率', width: 120, align: 'center', templet: function(d){
                    return d.fee_rate ? (d.fee_rate * 100).toFixed(2) + '%' : '0%';
                }},
                {field: 'status', title: '状态', width: 100, align: 'center', templet: '#statusTpl'},
                {field: 'remark', title: '备注', minWidth: 200, align: 'center', templet: function(d){
                    return '<div style="word-break: break-all;">' + (d.remark || '-') + '</div>';
                }},
                {field: 'create_time', title: '创建时间', width: 180, align: 'center'},
                {title: '操作', width: 300, templet: '#currentTableBar', fixed: "right", align: "center"}
            ]],
            limits: [10, 20, 30, 50, 100, 500],
            limit: 10,
            page: true,
            done: function (index, layero) {
                //表头部分
                //动态监听表头高度变化，冻结行跟着改变高度
                $(".layui-table-header tr").resize(function () {
                    $(".layui-table-header tr").each(function (index, val) {
                        $($(".layui-table-fixed .layui-table-header table tr")[index]).height($(val).height());
                    });
                });
                //初始化高度，使得冻结行表头高度一致
                $(".layui-table-header tr").each(function (index, val) {
                    $($(".layui-table-fixed .layui-table-header table tr")[index]).height($(val).height());
                });

                //表体部分
                //动态监听表体高度变化，冻结行跟着改变高度
                $(".layui-table-body tr").resize(function () {
                    $(".layui-table-body tr").each(function (index, val) {
                        $($(".layui-table-fixed .layui-table-body table tr")[index]).height($(val).height());
                    });
                });
                //初始化高度，使得冻结行表体高度一致
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

        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                layer.open({
                    type: 2,
                    title: "添加商户",
                    content: '/merchant/addForm',
                    maxmin: true,
                    area: ['900px', '90%'],
                    btn: ["确定", "取消"],
                    yes: function(index, layero) {
                        var formSubmit = layer.getChildFrame('form', index);
                        formSubmit.find('button[lay-submit]')[0].click();
                    },
                    end: function(){
                        table.reload('currentTableId');
                    }
                })
            }
        });

        table.on('tool(currentTableFilter)', function (obj) {
            var data = obj.data;
            
            if (obj.event === 'edit') {
                layer.open({
                    type: 2,
                    title: "编辑商户",
                    content: '/merchant/editForm?id=' + data.id,
                    maxmin: true,
                    area: ['900px', '90%'],
                    btn: ["确定", "取消"],
                    yes: function(index, layero) {
                        var formSubmit = layer.getChildFrame('form', index);
                        var submited = formSubmit.find('button[lay-submit]')[0];
                        submited.click();
                    },
                    end: function(){
                        table.reload('currentTableId');
                    }
                })
            } else if (obj.event === 'password') {
                layer.prompt({title: '请输入新密码', formType: 1}, function(value, index){
                    $.post('/merchant/updatePassword', {
                        id: data.id,
                        password: value
                    }, function(res){
                        if(res.code == 1){
                            layer.msg('密码修改成功', {icon: 1});
                            layer.close(index);
                            table.reload('currentTableId');
                        } else {
                            layer.msg(res.msg || '密码修改失败', {icon: 2});
                        }
                    });
                });
            } else if (obj.event === 'disable') {
                $.post('/merchant/updateStatus', {
                    id: data.id,
                    status: 0
                }, function(res){
                    if(res.code == 1){
                        layer.msg('已禁用', {icon: 1});
                        table.reload('currentTableId');
                    } else {
                        layer.msg(res.msg || '操作失败', {icon: 2});
                    }
                });
            } else if (obj.event === 'enable') {
                $.post('/merchant/updateStatus', {
                    id: data.id,
                    status: 1
                }, function(res){
                    if(res.code == 1){
                        layer.msg('已启用', {icon: 1});
                        table.reload('currentTableId');
                    } else {
                        layer.msg(res.msg || '操作失败', {icon: 2});
                    }
                });
            }
        });

    });
</script>

</body>
</html>
