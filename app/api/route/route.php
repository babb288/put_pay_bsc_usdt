<?php


use think\facade\Route;
use app\api\middleware\Validate;


Route::group(function(){

    Route::rule('put/index','put/index');
    Route::rule('pay/index','pay/index');



})->middleware(Validate::class);

