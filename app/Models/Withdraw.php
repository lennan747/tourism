<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    // 退款状态
    const REVIEW_STATUS_APPLICATION    = 'Application';
    const REVIEW_STATUS_BY             = 'by';
    const REVIEW_STATUS_REFUSE         = 'Refuse';

    public static $reviewStatusMap = [
        self::REVIEW_STATUS_APPLICATION => '审核',
        self::REVIEW_STATUS_BY          => '通过',
        self::REVIEW_STATUS_REFUSE      => '拒绝'
    ];
    //
    protected $fillable = [
        'user_id',
        'bank_card',
        'application_amount',
        'application_date',
        'transfer_amount',
        'transfer_date',
        'handling_fee',
        'status',
        'reason'
    ];

    protected $casts = [
        'bank_card'   => 'json'
    ];


    // 所属用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
