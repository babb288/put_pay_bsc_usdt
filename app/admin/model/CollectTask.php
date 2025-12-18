<?php

namespace app\admin\model;

use think\Model;

class CollectTask extends Model
{
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 字段类型转换
    protected $type = [
        'wallet_count' => 'integer',
        'collect_amount' => 'float',
        'contract_fee' => 'float',
        'platform_fee' => 'float',
        'status' => 'integer',
    ];
}