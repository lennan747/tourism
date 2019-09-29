<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    //用户资源
    $router->resource('users', UsersController::class);

    /***** 订单资源 开始 ******/
    // 订单列表
    $router->get('orders','OrdersController@index')->name('admin.orders.index');
    // 订单详情
    $router->get('orders/{order}','OrdersController@show')->name('admin.orders.show');
    // 订单审核
    $router->post('orders/{order}/review', 'OrdersController@review')->name('admin.orders.review');
    /***** 订单资源 结束 ******/
});
