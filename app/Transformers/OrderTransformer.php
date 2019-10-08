<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order)
    {
        return [
            'id'             => $order->id,
            'no'             => $order->no,
            'pay_status'     => $order->pay_status,
            'paid_at'        => $order->paid_at,
            'type'           => $order->type,
            'created_at'     => (string) $order->created_at,
            'updated_at'     => (string) $order->updated_at,
        ];
    }

}
