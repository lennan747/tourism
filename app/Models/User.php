<?php

namespace App\Models;

use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const USER_IDENTITY_ORDINARY    = 'ordinary';
    const USER_IDENTITY_STORE       = 'store';
    const USER_IDENTITY_DEPARTMENT  = 'department';
    const USER_IDENTITY_DIRECTOR    = 'director';
    const USER_IDENTITY_PLAYER      = 'player';

    public static $userIdentityMap = [
        self::USER_IDENTITY_ORDINARY      => '普通用户',
        self::USER_IDENTITY_STORE         => '门店经理',
        self::USER_IDENTITY_DEPARTMENT    => '部门经理',
        self::USER_IDENTITY_DIRECTOR      => '运营总监',
        self::USER_IDENTITY_PLAYER        => '酱紫玩家'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'identity',
        'phone',
        'email',
        'parent_id',
        'tree',
        'money',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'parent_id'         => 'integer',
        'money'             => 'decimal:2'
    ];

    //public function

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function username()
    {
        return 'phone';
    }

    // 订单
    public function order(){
        return $this->hasMany(Order::class,'user_id','id');
    }

    // 上级
    public function teamTop()
    {
        return $this->hasMany(Team::class, 'player_id','id');
    }

    // 队员
    public function teamPlayer()
    {
        return $this->hasMany(Team::class, 'top_id','id');
    }

    // 邀请码
    public function invite_code()
    {
        return $this->hasOne(InviteCode::class,'user_id','id');
    }

    // 银行卡
    public function bank_card()
    {
        return $this->hasMany(BankCard::class,'user_id','id');
    }

    // 提现记录
    public function withdraw()
    {
        return $this->hasMany(Withdraw::class,'user_id','id');
    }

    public function parent()
    {
        return $this->hasOne(User::class,'id','parent_id');
    }

}
