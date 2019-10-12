<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InviteCode extends Model
{
    //
    protected $fillable = [
        'code',
        'type',
        'user_id',
        'views',
        'url'
    ];


    /**
     * 创建订单前生成邀请码
     */
    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 code 字段为空
            if (!$model->code) {
                // 调用 findAvailableNo 生成邀请码
                $model->code = static::findAvailableCode();
                // 如果生成失败，则终止生成邀请码
                if (!$model->code) {
                    return false;
                }else{
                    $model->url = 'http://192.168.56.1:8888?invite_code='.$model->code;
                }
            }
        });
    }

    public static function findAvailableCode($length = 16)
    {
        do {
            // 生成一个指定长度的随机字符串，并转成大写
            $code = strtoupper(Str::random($length));
            // 如果生成的码已存在就继续循环
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    // 邀请码所属用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
