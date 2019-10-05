<?php
namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['sku'];

    public function transform(Product $product)
    {
        $images = [];
        foreach ($product->image as $value){
            $images[]['url'] = env('APP_URL').'/uploads/'.$value;
        }

        return [
            'id'                    => $product->id,
            'title'                 => $product->title,
            'type'                  => $product->type,
            'index_image'           => env('APP_URL').'/uploads/'.$product->index_image,
            'image'                 => $images,
            'rating'                => $product->rating,
            'sold_count'            => $product->sold_count,
            'product_detail'        => $product->product_detail,
            'cost_detail'           => $product->cost_detail,
            'journey_detail'        => $product->journey_detail,
            'on_recommend'          => $product->on_recommend,
            'description'           => $product->description,
            'price'                 => $product->price,
            'created_at'            => (string) $product->created_at,
            'updated_at'            => (string) $product->updated_at,
        ];
    }

    public function includeSku(Product $product)
    {
        return $this->collection($product->skus()->get(), new ProductSkuTransformer());
    }
}
