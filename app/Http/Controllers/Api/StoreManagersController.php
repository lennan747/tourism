<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreManagerRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class StoreManagersController extends Controller
{
    //
    protected $price = 3980;

    public function store(StoreManagerRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        // 只用普通用户能购买
        // TODO 酱紫玩家可以升级
        if($this->user()->identity !== User::USER_IDENTITY_ORDINARY){
            return $this->response->error('请勿重复购买', 422);
        }

        // 422
        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 返回401
        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 创建订单
        $order = new Order([
            'user_id'        => $this->user()->id,
            'total_amount'   => $this->price,
            'pay_status'     => Order::PAY_STATUS_UNPAID,
            'type'           => Order::ORDER_TYPE_MEMBER
        ]);

        $order->save();

        // 返回订单详情
        return $this->response->item($order, new TopicTransformer())
            ->setStatusCode(201);
    }
}
