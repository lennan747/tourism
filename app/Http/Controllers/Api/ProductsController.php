<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;

class ProductsController extends Controller
{
    //
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

    public function show(Request $request, Product $product)
    {
        $query = $product->query();

        $query->where([
            ['on_sale', '=' ,true],
            ['id', '=', $request->id]
        ]);

        $product = $query->first();

        return $product ? $this->response->item($product,new ProductTransformer()) : Null;
    }
}
