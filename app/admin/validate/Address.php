<?php

namespace app\admin\validate;

use think\Validate;

class Address extends Validate
{
    protected $rule = [
        'username'      => ['require', 'max' => 100],
        'bind_data'     => ['max' => 65535],
        'address'       => ['require', 'regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'balance'       => ['float', '>=:0'],
        'bnb_balance'  => ['float', '>=:0'],
        'callback_url' => ['checkUrl', 'max' => 500],
        'redirect_url' => ['checkUrl', 'max' => 500],
        'status'        => ['require', 'in' => [0, 1]],
        'is_authorized' => ['require', 'in' => [0, 1]],
        'id'            => ['require', 'integer', '>:0'],
        'page'          => 'require|number',
        'limit'         => 'require|number',
    ];

    protected $message = [
        'username.require'      => ['code' => -1, 'msg' => '用户名不能为空'],
        'username.max'          => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'bind_data.max'         => ['code' => -1, 'msg' => '绑定数据长度超出限制'],
        'address.require'       => ['code' => -1, 'msg' => '地址不能为空'],
        'address.regex'         => ['code' => -1, 'msg' => '地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'balance.float'         => ['code' => -1, 'msg' => '代币余额必须是数字'],
        'balance.>='            => ['code' => -1, 'msg' => '代币余额不能小于0'],
        'bnb_balance.float'     => ['code' => -1, 'msg' => 'BNB余额必须是数字'],
        'bnb_balance.>='        => ['code' => -1, 'msg' => 'BNB余额不能小于0'],
        'callback_url.url'      => ['code' => -1, 'msg' => '回调地址格式不正确'],
        'callback_url.max'      => ['code' => -1, 'msg' => '回调地址长度不能超过500个字符'],
        'redirect_url.url'      => ['code' => -1, 'msg' => '跳转地址格式不正确'],
        'redirect_url.max'      => ['code' => -1, 'msg' => '跳转地址长度不能超过500个字符'],
        'status.require'        => ['code' => -1, 'msg' => '状态不能为空'],
        'status.in'             => ['code' => -1, 'msg' => '状态值不正确'],
        'is_authorized.require' => ['code' => -1, 'msg' => '授权状态不能为空'],
        'is_authorized.in'      => ['code' => -1, 'msg' => '授权状态值不正确'],
        'id.require'            => ['code' => -1, 'msg' => 'ID不能为空'],
        'id.integer'            => ['code' => -1, 'msg' => 'ID必须是整数'],
        'id.>'                  => ['code' => -1, 'msg' => 'ID必须大于0'],
        'page'                  => ['code' => -1, 'msg' => '页数不正确'],
        'limit'                 => ['code' => -1, 'msg' => '每页数量不正确'],
    ];

    protected $scene = [
        'list'              => ['page', 'limit'],
        'updateStatus'      => ['id', 'status'], // 更新状态需要验证id和status
        'refreshBalance'    => ['id'], // 刷新余额需要验证id
        'addAuthTask'       =>  ['username']
    ];

    /**
     * 自定义URL验证（允许为空）
     */
    protected function checkUrl($value, $rule, $data = [])
    {
        if (empty($value)) {
            return true; // 允许为空
        }
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return 'URL格式不正确';
        }
        return true;
    }
}

