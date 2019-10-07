<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    //
    protected $fillable = ['id','title', 'description', 'price', 'stock' ,'profit'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
