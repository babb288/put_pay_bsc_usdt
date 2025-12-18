<?php

namespace app\admin\validate;

use think\Validate;

class CollectTask extends Validate
{
    protected $rule = [
        'id'            => ['require', 'integer', '>:0'],
        'page'          => 'require|number',
        'limit'         => 'require|number',
        'username'      => ['max' => 100],
        'status'        => ['in' => [0, 1, 2, 3]],
    ];

    protected $message = [
        'id.require'             => ['code' => -1, 'msg' => 'ID不能为空'],
        'id.integer'             => ['code' => -1, 'msg' => 'ID必须是整数'],
        'id.>'                   => ['code' => -1, 'msg' => 'ID必须大于0'],
        'page.require'           => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'            => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'          => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'           => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'           => ['code' => -1, 'msg' => '商户用户名长度不能超过100个字符'],
        'status.in'              => ['code' => -1, 'msg' => '状态值不正确（0-待处理，1-进行中，2-成功，3-失败）'],
    ];

    protected $scene = [
        'list'         => ['page', 'limit', 'username', 'status'],
        'refreshStatus' => ['id'],
    ];
    
}

