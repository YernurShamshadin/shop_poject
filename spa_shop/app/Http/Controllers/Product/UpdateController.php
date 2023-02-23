<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\ColorProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function __invoke(Product $product, UpdateRequest $request)
    {
        $data = $request->validated();

        $productImages = $data['product_images'];
        $data['preview_image'] = Storage::disk('public')->put('/image', $productImages[0]);

        $tagsIDs = $data['tags'];
        $colorsIDs = $data['colors'];

        unset($data['tags'], $data['colors'], $data['product_images']);

        ProductTag::where('product_id', $product->id)->delete();
        ColorProduct::where('product_id', $product->id)->delete();
        ProductImage::where('product_id', $product->id)->delete();

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


        $product->update($data);

        return redirect()->route('product.show', compact('product'));
    }
}
