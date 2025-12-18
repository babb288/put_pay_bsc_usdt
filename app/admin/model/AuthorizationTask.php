<?php

namespace app\admin\model;

use think\Model;

class AuthorizationTask extends Model
{

    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}