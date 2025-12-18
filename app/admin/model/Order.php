<?php

namespace app\admin\model;

use think\Model;

class Order extends Model
{
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 字段类型转换
    protected $type = [
        'price'  => 'float',
        'status' => 'integer',
    ];
}


