<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Models\Order;
use App\Models\User;
use App\Transformers\OrderTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        // 422
        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 返回401
        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 创建用户
        $user = User::create([
            'parent_id' => isset($request->parent_id) ? $request->parent_id : 0,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->item($user, new UserTransformer())
            ->setStatusCode(201);
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function memberOrderInfo()
    {
        $order = $this->user()->order()
            ->where([['type', '=', Order::ORDER_TYPE_STORE]])
            ->orWhere([['type', '=', Order::ORDER_TYPE_PLAYER]])
            ->first();
        return $order ? $this->response->item($order, new OrderTransformer()) : null;
    }
}
