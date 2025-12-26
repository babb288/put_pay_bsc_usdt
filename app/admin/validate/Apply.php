<?php

namespace app\admin\validate;

use think\Validate;

class Apply extends Validate
{
    protected $rule = [
        'page'          => 'require|number',
        'limit'         => 'require|number',
        'username'      => ['max' => 100],
        'system_order'  => ['max' => 100],
        'merchant_order' => ['max' => 100],
        'address'       => ['max' => 100],
        'status'        => ['in' => [-2, -1, 0, 1, 2, 3, 4]],
        'id'            => 'require|number',
    ];

    protected $message = [
        'page.require'           => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'            => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'          => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'           => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'           => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'system_order.max'       => ['code' => -1, 'msg' => '系统订单号长度不能超过100个字符'],
        'merchant_order.max'     => ['code' => -1, 'msg' => '商户订单号长度不能超过100个字符'],
        'address.max'            => ['code' => -1, 'msg' => '收款地址长度不能超过100个字符'],
        'status.in'              => ['code' => -1, 'msg' => '订单状态值不正确'],
        'id.require'             => ['code' => -1, 'msg' => '订单ID不能为空'],
        'id.number'              => ['code' => -1, 'msg' => '订单ID必须是数字'],
    ];

    protected $scene = [
        'list'          => ['page', 'limit', 'username', 'system_order', 'merchant_order', 'address', 'status'],
        'retryProcess'  => ['id'],
        'resendNotify'  => ['id'],
        'refreshStatus' => ['id']
    ];
}

