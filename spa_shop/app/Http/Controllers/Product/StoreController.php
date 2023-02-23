<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Models\ColorProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();

        $productImages = $data['product_images'];
        $data['preview_image'] = Storage::disk('public')->put('/image', $productImages[0]);

        $tagsIDs = $data['tags'];
        $colorsIDs = $data['colors'];

        unset($data['tags'], $data['colors'], $data['product_images']);

        $product = Product::firstOrCreate([
            'title' => $data['title']
            ], $data);

        foreach ($tagsIDs as $tagsID) {
            ProductTag::firstOrCreate([
                'product_id' => $product->id,
                'tag_id' => $tagsID,
            ]);
        }

        foreach ($colorsIDs as $colorsID) {
            ColorProduct::firstOrCreate([
                'product_id' => $product->id,
                'color_id' => $colorsID,
            ]);
        }

        foreach ($productImages as $productImage) {
            $currentImagesCount = ProductImage::where('product_id', $product->id)->count();

            if($currentImagesCount > 3) continue;
            $filePath = Storage::disk('public')->put('/image', $productImage);
            ProductImage::create([
                'product_id' => $product->id,
                'file_path' => $filePath,
            ]);
        }

        return redirect()->route('product.index');
    }
}