<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'            => $user->id,
            'phone'         => $user->phone,
            'identity'      => $user->identity,             // 用户身份信息
            'money'         => $user->money,                // 用户余额
            'avatar'        => $user->avatar,
            // 会员订单信息
            //'member_order'  => $user->order()->where([['user_id','=' ,$user->id], ['type','=', Order::ORDER_TYPE_MEMBER]])->first(),
            'created_at'    => (string) $user->created_at,
            'updated_at'    => (string) $user->updated_at,
        ];
    }
}
