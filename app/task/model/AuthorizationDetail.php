<?php

namespace app\task\model;

use think\Model;

class AuthorizationDetail extends Model
{

    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 字段类型转换
    protected $type = [
        'is_bnb' => 'integer',
        'is_approve' => 'integer',
    ];
}

