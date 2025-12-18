<?php

namespace app\admin\validate;

use think\Validate;

class AuthorizationDetail extends Validate
{
    protected $rule = [
        'page'          => 'require|number',
        'limit'         => 'require|number',
        'username'      => ['max' => 100],
        'address'       => ['regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'is_bnb'        => ['in' => [0, 1]],
        'is_approve'    => ['in' => [0, 1]],
    ];

    protected $message = [
        'page.require'           => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'            => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'          => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'           => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'           => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'address.regex'          => ['code' => -1, 'msg' => '地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'is_bnb.in'              => ['code' => -1, 'msg' => 'BNB到账状态值不正确（0-未到账，1-已到账）'],
        'is_approve.in'          => ['code' => -1, 'msg' => '授权状态值不正确（0-未授权，1-已授权）'],
    ];

    protected $scene = [
        'list' => ['page', 'limit', 'username', 'address', 'is_bnb', 'is_approve'],
    ];
}

