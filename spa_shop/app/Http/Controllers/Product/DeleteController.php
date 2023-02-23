<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\ColorProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTag;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function __invoke(Product $product)
    {
        ProductTag::where('product_id', $product->id)->delete();
        ColorProduct::where('product_id', $product->id)->delete();
        ProductImage::where('product_id', $product->id)->delete();

        $product->delete();

        return redirect()->route('product.index');
    }
}
