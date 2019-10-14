<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    //
    protected $fillable = [
        'top_id',
        'player_id',
        'depth',
        'role',
    ];

    public function top()
    {
        return $this->belongsTo(User::class,'top_id','id');
    }

    public function player()
    {
        return $this->belongsTo(User::class,'player_id','id');
    }
}
