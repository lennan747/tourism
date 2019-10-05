<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreMemberRequest;
use App\Http\Requests\Api\StoreProductRequest;
use App\Models\Order;
use App\Models\User;
use App\Transformers\OrderTransformer;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function storeProduct(StoreProductRequest $request)
    {
        $verifyData = \Cache::get($request->captcha_key);

        // 422
        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 返回401
        if (!hash_equals($verifyData['code'], $request->captcha_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 获取商品信息
        
        // 创建订单
        $order = new Order([
            'total_amount'   => Order::$memberPriceMap[$type],  // 订单总价
            'pay_status'     => Order::PAY_STATUS_UNPAID,       // 订单状态，未支付
            'type'           => Order::ORDER_TYPE_TOURISM       // 订单类型,购买会员订单
        ]);

        //关联用户
        $order->user_id = $this->user()->id;
        $order->save();

        // 返回订单详情
        return $this->response->item($order, new OrderTransformer())->setStatusCode(201);
    }

    // 门店经理订单
    public function storeMember(StoreMemberRequest $request){
        $verifyData = \Cache::get($request->captcha_key);
        // 订单类型
        $type = $request->type == 'store' ? Order::ORDER_TYPE_STORE : Order::ORDER_TYPE_PLAYER;

        // 只用普通用户能购买
        // TODO
        if($this->user()->identity !== User::USER_IDENTITY_ORDINARY){
            return $this->response->error('请勿重复购买', 422);
        }

        // 检查当前用户门店订单是否存
        if($this->user()->order()
            ->where(function ($query){
                $query->where('type','=',Order::ORDER_TYPE_STORE)
                    ->orWhere('type','=',Order::ORDER_TYPE_PLAYER);
            })->exists()){
            return $this->response->error('请勿重复购买', 422);
        }

        // 422
        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 返回401
        if (!hash_equals($verifyData['code'], $request->captcha_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 创建订单
        $order = new Order([
            'total_amount'   => Order::$memberPriceMap[$type],  // 订单总价
            'pay_status'     => Order::PAY_STATUS_UNPAID,       // 订单状态，未支付
            'type'           => $type                           // 订单类型,购买会员订单
        ]);

        //关联用户
        $order->user_id = $this->user()->id;
        $order->save();

        // 返回订单详情
        return $this->response->item($order, new OrderTransformer())->setStatusCode(201);
    }

    /**
     * 订单前验证码
     * @param CaptchaBuilder $captchaBuilder
     * @return mixed
     */
    public function captcha(CaptchaBuilder $captchaBuilder){
        $key         = 'order-captcha-'.str_random(15);
        $captcha     = $captchaBuilder->build();
        $expiredAt   = now()->addMinutes(2);
        \Cache::put($key, ['code' => $captcha->getPhrase()], $expiredAt);

        return $this->response->array([
            'captcha_key'           => $key,
            'expired_at'            => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ])->setStatusCode(201);
    }
}
