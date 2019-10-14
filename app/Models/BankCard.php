<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankCard extends Model
{

    // 退款状态
    const CARD_TYPE_WECHAT    = 'wechat';
    const CARD_TYPE_ALIPAY    = 'alipay';
    const CARD_TYPE_BANK      = 'bank';


    public static $cardTypeMap = [
        self::CARD_TYPE_WECHAT => '微信',
        self::CARD_TYPE_ALIPAY => '支付宝',
        self::CARD_TYPE_BANK   => '银行卡'
    ];

    //
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'name',
        'card_name',
        'account',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
