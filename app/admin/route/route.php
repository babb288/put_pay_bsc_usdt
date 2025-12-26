<?php


use think\facade\Route;
use app\admin\middleware\Check;
use app\admin\middleware\Validate;


Route::rule('login/admin','login/admin');
Route::rule('','admin/jumpLogin');


Route::group(function(){
    Route::get('admin/login','admin/login');
    Route::get('admin/index','admin/index');
    Route::get('admin/report','report/index');
    Route::get('merchant/index','merchant/index');
    Route::get('merchant/addForm','merchant/addForm');
    Route::get('merchant/editForm','merchant/editForm');
    Route::get('wallet/index','wallet/index');
    Route::get('wallet/collect','wallet/collect');
    Route::get('wallet/getMerchantList','wallet/getMerchantList');
    Route::get('collect_task/index','collect_task/index');
    Route::get('authorization_detail/index','authorization_detail/index');
    Route::get('authorization_task/index','authorization_task/index');
    Route::get('send_detail/index','send_detail/index');
    Route::get('apply/index','apply/index');
    Route::get('order/index','order/index');
    Route::get('address/index','address/index');
    Route::get('address/getMerchantList','address/getMerchantList');

    Route::group(function(){

        // 商户管理路由
        Route::rule('merchant/list','merchant/list');
        Route::rule('merchant/add','merchant/add');
        Route::rule('merchant/edit','merchant/edit');
        Route::rule('merchant/delete','merchant/delete');
        Route::rule('merchant/updatePassword','merchant/updatePassword');
        Route::rule('merchant/updateStatus','merchant/updateStatus');
        Route::rule('merchant/submitHash','merchant/submitHash');

        // 钱包管理路由
        Route::rule('wallet/list','wallet/list');
        Route::rule('wallet/refresh','wallet/refresh');
        Route::rule('wallet/doCollect','wallet/doCollect');
        Route::rule('wallet/oneKeySend','wallet/oneKeySend');
        
        // 归集任务路由
        Route::rule('collect_task/list','collect_task/list');
        Route::rule('collect_task/refreshStatus','collect_task/refreshStatus');
        
        // 授权明细路由
        Route::rule('authorization_detail/list','authorization_detail/list');
        
        // 授权任务路由
        Route::rule('authorization_task/list','authorization_task/list');
        
        // 下发明细路由
        Route::rule('send_detail/list','send_detail/list');
        
        // 代付订单路由
        Route::rule('apply/list','apply/list');
        Route::rule('apply/retryProcess','apply/retryProcess');
        Route::rule('apply/resendNotify','apply/resendNotify');
        Route::rule('apply/refreshStatus','apply/refreshStatus');


        // 代收订单路由
        Route::rule('order/list','order/list');
        
        // 地址管理路由
        Route::rule('address/list','address/list');
        Route::rule('address/updateStatus','address/updateStatus');
        Route::rule('address/refreshBalance','address/refreshBalance');
        Route::rule('address/addAuthTask','address/addAuthTask');

        
    })->middleware(Validate::class);


})->middleware(Check::class);