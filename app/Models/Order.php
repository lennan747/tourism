<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // 退款状态
    const REFUND_STATUS_PENDING    = 'pending';
    const REFUND_STATUS_APPLIED    = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS    = 'success';
    const REFUND_STATUS_FAILED     = 'failed';

    // 发货状态
    const SHIP_STATUS_PENDING   = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED  = 'received';

    // 支付状态
    const PAY_STATUS_UNPAID = 'unpaid';
    const PAY_STATUS_PAID   = 'paid';

    // 订单类型
    const ORDER_TYPE_STORE  = 'store';
    const ORDER_TYPE_PLAYER = 'player';
    const ORDER_TYPE_TOURISM = 'tourism';
    const ORDER_TYPE_PRODUCT = 'product';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    public static $payStatusMap = [
        self::PAY_STATUS_UNPAID => '未支付',
        self::PAY_STATUS_PAID   => '已支付'
    ];

    public static $orderTypeMap = [
        self::ORDER_TYPE_STORE      => '购买门店经理',
        self::ORDER_TYPE_PLAYER     => '购买酱紫玩家',
        self::ORDER_TYPE_TOURISM    => '购买旅游',
        self::ORDER_TYPE_PRODUCT    => '购买商品'
    ];

    // 两种会员的价格
    public static $memberPriceMap = [
        self::ORDER_TYPE_STORE  => 3980,
        self::ORDER_TYPE_PLAYER => 999
    ];

    protected $fillable = [
        'no',
        'address',
        'total_amount',
        'total_profit',
        'remark',
        'pay_status',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'type',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    /**
     * 创建订单前生成订单流水号
     */
    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    /**
     * 流水单号生成器
     * @return bool|string
     * @throws \Exception
     */
    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
