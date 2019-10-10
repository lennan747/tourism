<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['cors','serializer:array','bindings'],
], function($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 用户登录
        $api->post('authorizations','AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        // 推荐商品列表
        $api->get('products/recommend', 'ProductsController@recommend')
            ->name('api.products.recommend');
        // 商品详情
        $api->get('products/{product}','ProductsController@show')
            ->name('api.products.show');
        // app 基本配置
        $api->get('config','ConfigController@index')
            ->name('api.configs.index');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');

            // 用户会员购买订单信息
            $api->get('user/member/order/info','UsersController@memberOrderInfo')
                ->name('api.user.member.order.info');
            // 用户旅游订单信息
            $api->get('user/tourism/order','UsersController@tourismOrderIndex')
                ->name('api.user.tourism.order.index');

            // 订单前的验证码
            $api->get('order/captcha','OrdersController@captcha')
                ->name('api.order.captcha');

            // 购买门店经理
            $api->post('order/store-member','OrdersController@storeMember')
                ->name('api.order.store.member');
            // 购买旅游产品
            $api->post('order/store-product','OrdersController@storeProduct')
                ->name('api.order.store.product');

            // 获取用户团队
            $api->get('team','TeamsController@index');
        });

    });
});
