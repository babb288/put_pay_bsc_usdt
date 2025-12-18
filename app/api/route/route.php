<?php


use think\facade\Route;
use app\api\middleware\Validate;


Route::group(function(){

    Route::rule('pay/collection/order','put/index');
    Route::rule('/pay/apply','pay/index');


})->middleware(Validate::class);

