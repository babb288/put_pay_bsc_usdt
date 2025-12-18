<?php /*a:1:{s:72:"C:\Users\bbab\PhpstormProjects\binance0\app\admin\view\merchant\add.html";i:1765906631;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加商户</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/lib/layui-v2.6.3/css/layui.css" media="all">
    <style>
        body {
            padding: 20px;
            background-color: #f2f2f2;
        }
        .layui-form {
            background: #fff;
            padding: 20px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
<form class="layui-form" lay-filter="merchantForm">
    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: red;">*</span>用户名</label>
        <div class="layui-input-block">
            <input type="text" name="username" placeholder="请输入用户名" lay-verify="required" autocomplete="off" class="layui-input" maxlength="100">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: red;">*</span>密码</label>
        <div class="layui-input-block">
            <input type="password" name="password" placeholder="请输入密码，至少6位" lay-verify="required|password" autocomplete="off" class="layui-input" maxlength="20">
            <div class="layui-form-mid layui-word-aux">密码长度至少6位</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: red;">*</span>下发地址</label>
        <div class="layui-input-block">
            <input type="text" name="address" placeholder="请输入下发地址（0x开头，42位）" lay-verify="required|address" autocomplete="off" class="layui-input" maxlength="42">
            <div class="layui-form-mid layui-word-aux">EVM地址格式，例如：0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">合约地址</label>
        <div class="layui-input-block">
            <input type="text" name="contract_address" placeholder="请输入合约地址（0x开头，42位）" lay-verify="contract_address" autocomplete="off" class="layui-input" maxlength="42">
            <div class="layui-form-mid layui-word-aux">EVM合约地址格式，例如：0x55d398326f99059fF775485246999027B3197955（可选）</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: red;">*</span>手续费率</label>
        <div class="layui-input-block">
            <input type="number" name="fee_rate" placeholder="请输入手续费率" lay-verify="required|number|fee_rate" autocomplete="off" class="layui-input" step="0.000001" min="0" max="1" value="0.001">
            <div class="layui-form-mid layui-word-aux">例如：0.001 表示 0.1%，0.01 表示 1%</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">IP白名单</label>
        <div class="layui-input-block">
            <textarea name="ip_whitelist" placeholder="请输入IP地址，每行一个，例如：&#10;127.0.0.1&#10;192.168.1.1" class="layui-textarea" rows="5"></textarea>
            <div class="layui-form-mid layui-word-aux">每行一个IP地址，留空表示不限制IP</div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea name="remark" placeholder="请输入备注信息" class="layui-textarea" rows="3"></textarea>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label"><span style="color: red;">*</span>状态</label>
        <div class="layui-input-block">
            <input type="radio" name="status" value="1" title="正常" checked>
            <input type="radio" name="status" value="0" title="禁用">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="submitBtn">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>

<script src="/static/lib/layui-v2.6.3/layui.js"></script>
<script>
layui.use(['form', 'layer', 'jquery'], function(){
    var form = layui.form;
    var layer = layui.layer;
    var $ = layui.jquery;

    // 自定义验证规则
    form.verify({
        password: function(value){
            if(value && value.length < 6){
                return '密码长度不能少于6位';
            }
        },
        address: function(value){
            if(value && !/^0x[a-fA-F0-9]{40}$/.test(value)){
                return '钱包地址格式不正确';
            }
        },
        contract_address: function(value){
            if(value && !/^0x[a-fA-F0-9]{40}$/.test(value)){
                return '合约地址格式不正确（必须是0x开头的42位十六进制地址）';
            }
        },
        fee_rate: function(value){
            if(value < 0 || value > 1){
                return '手续费率必须在0-1之间';
            }
        }
    });

    // 监听提交
    form.on('submit(submitBtn)', function(data){
        var formData = data.field;
        
        // 处理IP白名单
        if(formData.ip_whitelist){
            var ipList = formData.ip_whitelist.split('\n').filter(function(ip){
                return ip.trim();
            }).map(function(ip){
                return ip.trim();
            });
            formData.ip_whitelist = ipList.length > 0 ? ipList : null;
        } else {
            formData.ip_whitelist = null;
        }

        // 提交数据
        $.post('/merchant/add', formData, function(res){
            if(res.code == 1){
                layer.msg('添加成功', {icon: 1}, function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.location.reload();
                });
            } else {
                layer.msg(res.msg || '添加失败', {icon: 2});
            }
        }).fail(function(){
            layer.msg('网络错误', {icon: 2});
        });

        return false;
    });
});
</script>
</body>
</html>

