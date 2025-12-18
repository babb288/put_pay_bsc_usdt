<?php

namespace app\admin\middleware;

use think\facade\Cookie;
use app\admin\service\JwtService;

class Check
{

    public function __construct(
        private JwtService $jwtService
    ){}

    public function handle($request, \Closure $next)
    {

        $controller = $request->controller(true);
        $action     = $request->action(true);

        $is_login_action = ($controller === 'admin' && $action === 'login');

        $token = Cookie::get('Authorization');

        if ($is_login_action && !$token) {
            return $next($request);
        }

        if (!$token) {
            return redirect('/admin/login');
        }

        $user_info = $this->jwtService->verifyToken($request, $token);

        if (!$user_info) {
            Cookie::delete('Authorization');
            return $is_login_action
                ? $next($request)
                : redirect('/admin/login');
        }

        if($is_login_action){
            return redirect('/admin/index');
        }

        $request->username = $user_info;

        return $next($request);
    }
}