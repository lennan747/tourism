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
        'limit'      => config('api.rate_limits.sign.limit'),
        'expires'    => config('api.rate_limits.sign.expires'),
    ], function($api) {
        // 创建注册图片验证码
        $api->post('captchas/register', 'CaptchasController@register')
            ->name('api.captchas.register');
        // 创建忘记密码验证码
        $api->post('captchas/reset_password', 'CaptchasController@reset_password')
            ->name('api.captchas.password.reset');

        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 用户登录
        $api->post('authorizations','AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 重置密码
        $api->post('users/reset_password', 'UsersController@reset_password')
            ->name('api.users.reset.password');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });
    $api->group([
        'middleware'  => 'api.throttle',
        'limit'       => config('api.rate_limits.access.limit'),
        'expires'     => config('api.rate_limits.access.expires'),
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
            // 用户会员信息，根据订单状态获取
            $api->get('user/member/order/info','UsersController@memberOrderInfo')
                ->name('api.user.member.order.info');
            // 订单前的验证码
            $api->get('order/captcha','OrdersController@captcha')
                ->name('api.order.captcha');
            // 门店经理订单
            $api->post('order/store-member','OrdersController@storeMember')
                ->name('api.order.store.member');
            // 旅游产品订单
            $api->post('order/store-product','OrdersController@storeProduct')
                ->name('api.order.store.product');
            // 用户旅游订单列表
            $api->get('order/tourism','OrdersController@tourism')
                ->name('api.order.tourism.index');
            // 获取用户团队
            $api->get('team','TeamsController@index');

            // 银行卡列表
            $api->get('bank/card','BankCardsController@index');
            // 添加银行卡
            $api->post('bank/card','BankCardsController@store');
            $api->put('bank/card/{card}','BankCardsController@update');
            $api->delete('bank/card/{card}','BankCardsController@destroy');

            // 提现
            $api->get('withdraw','WithdrawsController@index');
            $api->post('withdraw','WithdrawsController@store');
        });

    });
});
