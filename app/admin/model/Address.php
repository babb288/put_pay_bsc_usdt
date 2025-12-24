<?php

namespace app\admin\model;

use think\Model;

class Address extends Model
{

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 字段类型转换
    protected $type = [
        'status'            => 'integer',
        'is_authorized'     => 'integer',
    ];
}

