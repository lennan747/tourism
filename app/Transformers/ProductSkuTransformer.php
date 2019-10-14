<?php
namespace App\Transformers;

use App\Models\ProductSku;
use League\Fractal\TransformerAbstract;

class ProductSkuTransformer extends TransformerAbstract
{
    public function transform(ProductSku $productSku)
    {
        return [
            'id'          => $productSku->id,
            'title'       => $productSku->title,
            'description' => $productSku->description,
            'price'       => $productSku->price,
            'stock'       => $productSku->stock
        ];
    }
}
