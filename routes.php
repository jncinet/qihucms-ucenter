<?php

use Illuminate\Routing\Router;

// 接口
Route::group([
    // 页面URL前缀
    'prefix' => 'ucenter',
    // 控制器命名空间
    'namespace' => 'Qihucms\UCenter\Controllers\Api',
    'middleware' => ['api'],
    'as' => 'api.'
], function (Router $router) {
    // 会员同步
    $router->post('{id}/user', 'UserController@bind')->name('ucenter.register');
    // 资金变动接口
    $router->post('{id}/account', 'AccountController@update')->name('ucenter.account');
});

// 后台
Route::group([
    // 后台使用laravel-admin的前缀加上扩展的URL前缀
    'prefix' => config('admin.route.prefix') . '/ucenter',
    // 后台管理的命名空间
    'namespace' => 'Qihucms\UCenter\Controllers\Admin',
    // 后台的中间件，限制管理权限才能访问
    'middleware' => config('admin.route.middleware'),
    'as' => 'admin.'
], function (Router $router) {
    // 配置
    $router->resource('site', 'SiteController');
});