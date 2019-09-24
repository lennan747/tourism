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

    // 订单资源
    // $router->resource('orders', OrdersController::class);
    $router->get('orders','OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}','OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/review', 'OrdersController@review')->name('admin.orders.review');
});
