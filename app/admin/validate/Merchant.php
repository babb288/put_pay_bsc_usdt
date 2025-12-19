<?php

namespace app\admin\validate;

use think\Validate;

class Merchant extends Validate
{
    protected $rule = [
        'uid'       => ['require', 'length' => 6, 'regex' => '/^\d{6}$/'],
        'username'  => ['require', 'max' => 100],
        'password'  => ['require', 'min' => 6],
        'address'   => ['require', 'regex' => '/^0x[a-fA-F0-9]{40}$/'],
        'contract_address' => ['checkContractAddress'],
        'fee_rate'  => ['require', 'float', '>=:0'],
        'status'    => ['require', 'in' => [0, 1]],
        'id'        => ['require', 'integer', '>:0'],
        'hash'      => ['require', 'regex' => '/^0x[a-fA-F0-9]{64}$/'],
        'page'      => 'require|number',
        'limit'     => 'require|number',
    ];

    protected $message = [
        'uid.require'       =>  ['code' => -1, 'msg' => '商户UID不能为空'],
        'uid.length'        =>  ['code' => -1, 'msg' => '商户UID必须是6位数字'],
        'uid.regex'         =>  ['code' => -1, 'msg' => '商户UID格式不正确'],
        'username.require'  =>  ['code' => -1, 'msg' => '用户名不能为空'],
        'username.max'      =>  ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'password.require'  =>  ['code' => -1, 'msg' => '密码不能为空'],
        'password.min'      =>  ['code' => -1, 'msg' => '密码长度不能少于6位'],
        'address.require'   =>  ['code' => -1, 'msg' => '下发地址不能为空'],
        'address.regex'     =>  ['code' => -1, 'msg' => '下发地址格式不正确'],
        'contract_address.checkContractAddress' => ['code' => -1, 'msg' => '合约地址格式不正确（必须是0x开头的42位十六进制地址）'],
        'fee_rate.require'  =>  ['code' => -1, 'msg' => '手续费率不能为空'],
        'fee_rate.float'    =>  ['code' => -1, 'msg' => '手续费率必须是数字'],
        'fee_rate.>='       =>  ['code' => -1, 'msg' => '手续费率不能小于0'],
        'status.require'    =>  ['code' => -1, 'msg' => '状态不能为空'],
        'status.in'         =>  ['code' => -1, 'msg' => '状态值不正确'],
        'id.require'        =>  ['code' => -1, 'msg' => 'ID不能为空'],
        'id.integer'        =>  ['code' => -1, 'msg' => 'ID必须是整数'],
        'id.>'              =>  ['code' => -1, 'msg' => 'ID必须大于0'],
        'hash.require'      =>  ['code' => -1, 'msg' => '交易哈希不能为空'],
        'hash.regex'        =>  ['code' => -1, 'msg' => '交易哈希格式不正确（必须是0x开头的66位十六进制字符串）'],
        'page'              =>  ['code' => -1,'msg' => '页数不正确'],
        'limit'             =>  ['code' => -1,'msg' => '每页数量不正确'],
    ];

    protected $scene = [
        'add'               => ['username', 'password', 'address', 'contract_address', 'fee_rate', 'status'], // uid由后端自动生成，不需要验证
        'edit'              => ['contract_address', 'fee_rate', 'status'], // uid、address和username不允许修改，不需要验证
        'updatePassword'   => ['password'],
        'updateStatus'      => ['id', 'status'], // 更新状态需要验证id和status
        'submitHash'        => ['id', 'hash'], // 哈希提交需要验证id和hash
        'list'              => ['page','limit']
    ];

    /**
     * 自定义合约地址验证（允许为空）
     */
    protected function checkContractAddress($value, $rule, $data = [])
    {
        if (empty($value)) {
            return true; // 允许为空
        }
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            return '合约地址格式不正确（必须是0x开头的42位十六进制地址）';
        }
        return true;
    }
    
}

