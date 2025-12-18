<?php

namespace app\admin\validate;

use think\Validate;

class Wallet extends Validate
{
    
    protected $rule = [
        'username'      => ['require', 'max' => 100],
        'address'       => ['require', 'regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'key'           => ['max' => 65535], // TEXT类型，最大长度
        'type'          => ['require', 'in' => ['put', 'pay']],
        'bnb_balance'   => ['float', '>=:0'],
        'usdt_balance' => ['float', '>=:0'],
        'id'            => ['require', 'integer', '>:0'],
        'page'          => 'require|number',
        'limit'         => 'require|number',
        'collect_type'  => ['require', 'in' => ['usdt']],
        'wallet_type'   => ['require', 'in' => ['put']],
    ];


    protected $message = [
        'username.require'      => ['code' => -1, 'msg' => '请选择商户用户名'],
        'username.max'          => ['code' => -1, 'msg' => '商户用户名长度不能超过100个字符'],
        'address.require'       => ['code' => -1, 'msg' => '钱包地址不能为空'],
        'address.regex'         => ['code' => -1, 'msg' => '钱包地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'key.max'               => ['code' => -1, 'msg' => '私钥长度超出限制'],
        'type.require'          => ['code' => -1, 'msg' => '类型不能为空'],
        'type.in'               => ['code' => -1, 'msg' => '类型值不正确（只能是put或pay）'],
        'bnb_balance.float'     => ['code' => -1, 'msg' => 'BNB余额必须是数字'],
        'bnb_balance.>='        => ['code' => -1, 'msg' => 'BNB余额不能小于0'],
        'usdt_balance.float'    => ['code' => -1, 'msg' => 'USDT余额必须是数字'],
        'usdt_balance.>='       => ['code' => -1, 'msg' => 'USDT余额不能小于0'],
        'id.require'             => ['code' => -1, 'msg' => 'ID不能为空'],
        'id.integer'             => ['code' => -1, 'msg' => 'ID必须是整数'],
        'id.>'                   => ['code' => -1, 'msg' => 'ID必须大于0'],
        'page'                   => ['code' => -1, 'msg' => '页数不正确'],
        'limit'                  => ['code' => -1, 'msg' => '每页数量不正确'],
        'collect_type.require'   => ['code' => -1, 'msg' => '归集类型不能为空'],
        'collect_type.in'        => ['code' => -1, 'msg' => '归集类型只能是USDT归集'],
        'wallet_type.require'    => ['code' => -1, 'msg' => '钱包类型不能为空'],
        'wallet_type.in'         => ['code' => -1, 'msg' => '钱包类型只能是代收钱包'],
    ];

    protected $scene = [
        'list'      => ['page', 'limit'],
        'refresh'   => ['id'], // 刷新钱包需要验证id
        'doCollect' => ['collect_type', 'wallet_type', 'username'], // 归集验证
        'oneKeySend'=> ['id'], // 一键下发验证
    ];
}

