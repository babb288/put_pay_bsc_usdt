<?php

namespace app\admin\validate;

use think\Validate;

class SendDetail extends Validate
{
    protected $rule = [
        'page'           => 'require|number',
        'limit'          => 'require|number',
        'username'       => ['max' => 100],
        'wallet_address' => ['regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'to_address'     => ['regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'txid'           => ['max' => 66],
        'status'         => ['in' => [0, 1, 2]],
    ];

    protected $message = [
        'page.require'            => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'             => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'           => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'            => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'            => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'wallet_address.regex'    => ['code' => -1, 'msg' => '代收钱包地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'to_address.regex'        => ['code' => -1, 'msg' => '下发地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'txid.max'                => ['code' => -1, 'msg' => '交易哈希长度不能超过66个字符'],
        'status.in'               => ['code' => -1, 'msg' => '状态值不正确（0-待发送，1-成功，2-失败）'],
    ];

    protected $scene = [
        'list' => ['page', 'limit', 'username', 'wallet_address', 'to_address', 'txid', 'status'],
    ];
}


