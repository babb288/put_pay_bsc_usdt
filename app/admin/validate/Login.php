<?php

namespace app\admin\validate;

use think\Validate;
class Login extends Validate
{

    protected $rule = [
        'username'=>['require','max' => 20],
        'password'=>['require','length'=> 32],
        'code'    =>['require','number','length'=>6]
    ];

    protected $message = [
        'username.require' => ['code' => -1,'msg' => '用户名不能为空'],
        'username.max'     => ['code' => -1,'msg' => '用户名错误'],
        'password.require' => ['code' => -1,'msg' => '密码不能为空'],
        'password.length'  => ['code' => -1,'msg' => '密码不正确'],
        'code.require'     => ['code' => -1,'msg' => '谷歌验证码不能为空'],
        'code.length'      => ['code' => -1,'msg' => '谷歌验证错误'],
        'code.number'      => ['code' => -1,'msg' => '谷歌验证错误']
    ];

    protected $scene = [
        'user' => ['username','password','code']
    ];


}