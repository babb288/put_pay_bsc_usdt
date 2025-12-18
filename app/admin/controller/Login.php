<?php

namespace app\admin\controller;


use app\admin\service\JwtService;
use app\admin\model\Admin;
use app\Request;
use think\facade\Cookie;

class Login
{

    public function __construct(
        private Admin $admin,
        private JwtService $jwtService,
        private Request $request
    ){}


    public function Admin(): \think\response\Json
    {
        $params = $this->request->param();

        $user_find = $this->admin->where($params)->find();

        if(!$user_find){
            return json(array('code'=>-1,'msg'=>'用户名或密码不正确'));
        }
        $token = $this->jwtService->generateToken(
            $this->request,
            array('username'=>$params['username'],'ip'=>$this->request->ip())
        );

        Cookie::set('Authorization',$token,config('jwt.expire'));

        return json(array('code' => 1,'msg' => '登录成功'));
    }


}