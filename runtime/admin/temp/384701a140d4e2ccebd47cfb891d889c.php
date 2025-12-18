<?php /*a:1:{s:74:"C:\Users\bbab\PhpstormProjects\binance0\app\admin\view\wallet\collect.html";i:1765980418;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>一键归集</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/lib/layui-v2.6.3/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/css/public.css" media="all">
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>一键归集</legend>
        </fieldset>

        <form class="layui-form layui-form-pane" action="" style="padding: 20px 30px 0 0;">
            <div class="layui-form-item">
                <label class="layui-form-label">归集类型</label>
                <div class="layui-input-block">
                    <input type="text" value="USDT归集" class="layui-input" readonly style="background-color: #f5f5f5;">
                    <input type="hidden" name="collect_type" value="usdt">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">钱包类型</label>
                <div class="layui-input-block">
                    <input type="text" value="代收钱包" class="layui-input" readonly style="background-color: #f5f5f5;">
                    <input type="hidden" name="wallet_type" value="put">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">商户用户名</label>
                <div class="layui-input-block">
                    <select name="username" lay-verify="required">
                        <option value="">请选择商户</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="collect-form">开始归集</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
            <legend>归集日志</legend>
        </fieldset>

        <div id="collect-log" style="padding: 15px; background-color: #f5f5f5; border-radius: 4px; min-height: 300px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 12px;">
            <div style="color: #999;">等待开始归集...</div>
        </div>
    </div>
</div>

<script src="/static/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>

<script>
    layui.use(['form', 'layer', 'jquery'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;

        var logContainer = $('#collect-log');

        // 添加日志函数
        function addLog(message, type) {
            var time = new Date().toLocaleTimeString();
            var color = '#333';
            if (type === 'success') {
                color = '#5FB878';
            } else if (type === 'error') {
                color = '#FF5722';
            } else if (type === 'warning') {
                color = '#FFB800';
            } else if (type === 'info') {
                color = '#1E9FFF';
            }
            var logItem = '<div style="color: ' + color + '; margin-bottom: 5px;">[' + time + '] ' + message + '</div>';
            logContainer.append(logItem);
            logContainer.scrollTop(logContainer[0].scrollHeight);
        }

        // 加载商户列表
        $.get('/wallet/getMerchantList', function(res){
            if(res.code == 1 && res.data){
                var selectHtml = '<option value="">请选择商户</option>';
                res.data.forEach(function(item){
                    selectHtml += '<option value="' + item.username + '">' + item.username + '</option>';
                });
                $('select[name="username"]').html(selectHtml);
                form.render('select');
            }
        });

        // 监听表单提交
        form.on('submit(collect-form)', function (data) {
            var loadIndex = layer.load(1, {shade: [0.5, '#000']});
            
            // 清空日志
            logContainer.html('');
            addLog('开始归集任务...', 'info');
            addLog('归集类型: USDT归集', 'info');
            addLog('钱包类型: 代收钱包', 'info');
            addLog('商户用户名: ' + data.field.username, 'info');

            $.post('/wallet/doCollect', data.field, function(res){
                layer.close(loadIndex);
                if(res.code == 1){
                    // 先显示归集数据
                    if(res.data && res.data.length > 0){
                        addLog('归集详情:', 'success');
                        res.data.forEach(function(item, index){
                            addLog('[' + (index + 1) + '] 地址: ' + item.address + ' 余额: ' + parseFloat(item.balance || 0).toFixed(2), 'success');
                        });
                        addLog('', 'success'); // 空行
                    }
                    
                    addLog('归集任务提交成功！', 'success');
                    addLog(res.msg || '归集完成', 'success');
                    
                    layer.msg('归集成功', {icon: 1});
                } else {
                    addLog('归集失败: ' + (res.msg || '未知错误'), 'error');
                    layer.msg(res.msg || '归集失败', {icon: 2});
                }
            }).fail(function(xhr, status, error){
                layer.close(loadIndex);
                addLog('网络错误: ' + error, 'error');
                layer.msg('网络错误', {icon: 2});
            });

            return false;
        });

    });
</script>

</body>
</html>

