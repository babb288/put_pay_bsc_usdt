<?php
namespace app\admin\middleware;

use think\exception\ValidateException;

class Validate
{

    public function handle($request, \Closure $next)
    {
        $validator = 'app\\' . app('http')->getName() . '\\validate\\' . $request->controller();

        try {
            validate($validator)->scene($request->action())->check($request->param());
        } catch (ValidateException $e) {
            return json($e->getError());
        }

        return $next($request);
    }



}