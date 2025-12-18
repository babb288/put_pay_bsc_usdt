<?php

namespace app\api\model;

use think\Model;

class Merchant extends Model
{
    protected $type = [
        'ip_whitelist' => 'array',
    ];

}