<?php

namespace app\admin\model;

use think\Model;

class Wallet extends Model
{
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 字段类型转换
    protected $type = [
        'bnb_balance' => 'float',
        'usdt_balance' => 'float',
    ];
}

