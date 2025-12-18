<?php

namespace app\admin\controller;

class Admin
{

    public function jumpLogin(): \think\response\Redirect
    {
        return redirect('/admin/login');
    }

    public function login(): \think\response\View
    {
        return View('login');
    }

    public function index(): \think\response\View
    {
        return View('index');
    }


}