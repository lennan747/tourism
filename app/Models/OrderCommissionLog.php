<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCommissionLog extends Model
{
    //
    protected $fillable = [
        'no',
        'order_id',
        'commission_user_id',
        'money',
        'type',
        'desc'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function commission_user()
    {
        return $this->belongsTo(User::class);
    }
}
