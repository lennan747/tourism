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

    // 订单分成日志
    $router->resource('order-commission-logs', OrderCommissionLogsController::class);
    /***** 订单资源 结束 ******/

    // 商品
    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');
    $router->post('products/upload', 'ProductsController@upload');

    // 提现
    //$router->resource('withdraws', WithdrawsController::class);
    $router->get('withdraws', 'WithdrawsController@index');
    $router->get('withdraws/{withdraw}/edit', 'WithdrawsController@edit');
    $router->put('withdraws/{withdraw}', 'WithdrawsController@update')->name('admin.withdraws.update');


    // 站点配置
    //$router->resource('configs', ConfigsController::class);
    $router->get('configs','ConfigsController@index');
    $router->get('configs/create','ConfigsController@create');
    $router->post('configs','ConfigsController@store');
    $router->put('configs/{config}','ConfigsController@update');
    $router->get('configs/site','ConfigsController@site');
    $router->get('configs/wechat','ConfigsController@wechat');
    $router->get('configs/manager','ConfigsController@manager');
    $router->get('configs/player','ConfigsController@player');
    $router->get('configs/wechat_rate','ConfigsController@wechat_rate');
    $router->get('configs/alipay_rate','ConfigsController@alipay_rate');
    $router->get('configs/bank_rate','ConfigsController@bank_rate');
    $router->get('configs/withdraw_date','ConfigsController@withdraw_date');
    $router->get('configs/withdraw_amount','ConfigsController@withdraw_amount');
    $router->get('configs/share_poster_bg','ConfigsController@share_poster_bg');
    $router->get('configs/about_us','ConfigsController@about_us');
    $router->get('configs/business_cooperation','ConfigsController@business_cooperation');
    $router->get('configs/consumer_hotline','ConfigsController@consumer_hotline');
    $router->get('configs/index_banner','ConfigsController@index_banner');
    $router->get('configs/help','ConfigsController@help');
});
