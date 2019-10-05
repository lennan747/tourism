<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;

class ProductsController extends Controller
{
    // 推荐商品列表
    public function recommend(Request $request, Product $products)
    {
        $query = $products->query();

        $query->where([
            ['on_sale', '=' ,true],
            ['on_recommend', '=', true]
        ]);

        $products = $query->paginate(10);

        return $products ? $this->response->paginator($products, new ProductTransformer()) : null;
    }

    // 商品详情
    public function show(Product $product)
    {
        if(!$product->on_sale){
            return $this->response->error('商品已下架', 422);
        }

        return $product ? $this->response->item($product,new ProductTransformer()) : Null;
    }
}
