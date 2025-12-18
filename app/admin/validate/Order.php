<?php

namespace app\admin\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'page'         => 'require|number',
        'limit'        => 'require|number',
        'username'     => ['max' => 100],
        'system_order' => ['max' => 100],
        'address'      => ['regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'status'       => ['in' => [0, 1, 2, 3]],
    ];

    protected $message = [
        'page.require'         => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'          => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'        => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'         => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'         => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'system_order.max'     => ['code' => -1, 'msg' => '系统订单号长度不能超过100个字符'],
        'address.regex'        => ['code' => -1, 'msg' => '钱包地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'status.in'            => ['code' => -1, 'msg' => '订单状态值不正确（0-待支付，1-已支付，2-已失败, 3-已取消）'],
    ];

    protected $scene = [
        'list' => ['page', 'limit', 'username', 'system_order', 'address', 'status'],
    ];
}


