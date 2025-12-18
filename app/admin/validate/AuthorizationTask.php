<?php

namespace app\admin\validate;

use think\Validate;

class AuthorizationTask extends Validate
{
    protected $rule = [
        'page'        => 'require|number',
        'limit'       => 'require|number',
        'username'    => ['max' => 100],
        'task_status' => ['in' => [0, 1, 2]],
    ];

    protected $message = [
        'page.require'        => ['code' => -1, 'msg' => '页数不能为空'],
        'page.number'         => ['code' => -1, 'msg' => '页数必须是数字'],
        'limit.require'       => ['code' => -1, 'msg' => '每页数量不能为空'],
        'limit.number'        => ['code' => -1, 'msg' => '每页数量必须是数字'],
        'username.max'        => ['code' => -1, 'msg' => '用户名长度不能超过100个字符'],
        'task_status.in'      => ['code' => -1, 'msg' => '任务状态值不正确（0-待处理/进行中，1-已完成，2-失败）'],
    ];

    protected $scene = [
        'list' => ['page', 'limit', 'username', 'task_status'],
    ];
}


