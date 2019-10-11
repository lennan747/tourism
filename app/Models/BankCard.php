<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankCard extends Model
{
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
