<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    //
    protected $fillable = ['id','title', 'description', 'price', 'stock' ,'profit'];

    // 所属商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
