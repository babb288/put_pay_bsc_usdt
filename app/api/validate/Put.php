<?php

namespace app\api\validate;


use think\Validate;

class Put extends Validate
{


    protected $rule = [
        'uid'         => ['require', 'length' => 6, 'integer'],
        'bind_data'   => ['require', 'max' => 50],
        'network'     => ['require', 'checkNetwork' => 'bsc'],
        'type'        => ['require'],
        'redirect_url'=> ['require', 'url'],
        'notify_url'  => ['require', 'url'],
        'body'        => ['require', 'max' => 20],
        'sign'        => ['require', 'length' => 32]
    ];


    protected $message = [
        'uid'               =>  ['code' => -1,'msg' => 'uid商户号不存在'],
        'uid.require'       =>  ['code' => -1,'msg' => '商户号不能为空'],
        'uid.length'        =>  ['code' => -1,'msg' => '商户号长度必须为6位'],
        'uid.integer'       =>  ['code' => -1,'msg' => '商户号必须是数字'],
        'bind_data'         =>  ['code' => -1,'msg' => '绑定数据不能为空'],
        'bind_data.require' =>  ['code' => -1,'msg' => '绑定数据不能为空'],
        'bind_data.max'     =>  ['code' => -1,'msg' => '绑定数据长度不能超过50个字符'],
        'network'            =>  ['code' => -1,'msg' => '网络类型不能为空'],
        'network.require'    =>  ['code' => -1,'msg' => '网络类型不能为空'],
        'network.checkNetwork' => ['code' => -1,'msg' => 'network传入错误'],
        'type'               =>  ['code' => -1,'msg' => '类型不能为空'],
        'type.require'       =>  ['code' => -1,'msg' => '类型不能为空'],
        'redirect_url'       =>  ['code' => -1,'msg' => '跳转地址不能为空'],
        'redirect_url.require' => ['code' => -1,'msg' => '跳转地址不能为空'],
        'redirect_url.url'   =>  ['code' => -1,'msg' => '跳转地址格式不正确'],
        'notify_url'         =>  ['code' => -1,'msg' => '通知地址不能为空'],
        'notify_url.require' =>  ['code' => -1,'msg' => '通知地址不能为空'],
        'notify_url.url'     =>  ['code' => -1,'msg' => '通知地址格式不正确'],
        'body'               =>  ['code' => -1,'msg' => '订单描述不能为空'],
        'body.require'       =>  ['code' => -1,'msg' => '订单描述不能为空'],
        'body.max'           =>  ['code' => -1,'msg' => '订单描述长度不能超过20个字符'],
        'sign'               =>  ['code' => -1,'msg' => '签名不能为空'],
        'sign.require'       =>  ['code' => -1,'msg' => '签名不能为空'],
        'sign.length'        =>  ['code' => -1,'msg' => '签名长度必须为32位'],
    ];

    protected function checkNetwork($value, $rule, $data=[]): bool|array
    {
        return $rule == $value ? true : ['code' =>-1,'msg' => 'network传入错误'];
    }


    protected $scene = [
        'index' => ['uid','bind_data','network','type','redirect_url','notify_url','body','sign'],
    ];





}