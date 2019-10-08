<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //
    protected $fillable = [
        'id',
        'name',
        'title',
        'value',
        'extra',
    ];

    public function getValueAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode(array_values($value));
    }
}
