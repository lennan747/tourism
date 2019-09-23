<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'         => $user->id,
            'phone'      => $user->phone,
            'identity'   => $user->identity,             // 用户身份信息
            'money'      => $user->money,                // 用户余额
            'avatar'     => $user->avatar,
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at,
        ];
    }
}
