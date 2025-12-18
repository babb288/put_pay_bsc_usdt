<?php

namespace app\api\validate;

use think\Validate;

class Pay extends Validate
{

    protected $rule = [
        'uid'            => 'require|length:6|integer',
        'merchant_order' => 'require|max:50',
        'network'        => 'require|checkNetwork:bsc',
        'type'           => 'require',
        'price'          => 'require|float',
        'address'        => 'require|length:42',
        'notify_url'     => 'require|url',
        'body'           => 'require|max:20',
        'sign'           => 'require|length:32'
    ];

    protected $message = [
        'address'                       => ['code' => -1,'msg' => '收款地址错误'],
        'uid'                           => ['code' => -1,'msg' => 'uid不正确'],
        'merchant_order'                => ['code' => -1,'msg' => '商户订单号不正确'],
        'network'                       => ['code' => -1,'msg' => '网络不正确'],
        'type'                          => ['code' => -1,'msg' => 'type不正确'],
        'price'                         => ['code' => -1,'msg' => 'price不正确'],
        'notify_url'                    => ['code' => -1,'msg' => 'notify_url不正确'],
        'body'                          => ['code' => -1,'msg' => 'body不正确'],
        'sign'                          => ['code' => -1,'msg' => 'sign签名不正确'],
    ];

    protected function checkNetwork($value, $rule, $data=[]): bool|array
    {
        return $rule == $value ? true : ['code' => -1,'msg' => 'network传入错误'];
    }

    protected  $scene = [
        'index' => ['uid','merchant_order','network','type','price','address','notify_url','body','sign'],
    ];

}