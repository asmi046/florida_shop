<?php

namespace App\Http\Controllers;

use App\Models\Product;

class TovarController extends Controller
{
    public function getPriductById($id)
    {
        $prosuct = Product::with(['tovar_categories', 'product_images'])->where('id', $id)->first();

        $main_img = asset('img/noPhoto.jpg');

        if (! empty($prosuct->img)) {
            $main_img = $prosuct->img;
        }

        return ['product' => $prosuct, 'main_img' => $main_img];
    }

    public function show($slug)
    {

        $prosuct = Product::with(['tovar_categories', 'product_images'])->where('slug', $slug)->first();

        if ($prosuct == null) {
            abort('404');
        }

        $images = $prosuct->product_images;
        // $up_sale = Product::where('category', $prosuct->category)->take(5)->get();
        $up_sale_cat = $prosuct->tovar_categories()->first();
        if ($up_sale_cat == null) {
            $up_sale = [];
        } else {
            $up_sale = $up_sale_cat->category_tovars()->take(5)->get();
        }

        return view('tovar', ['product' => $prosuct, 'images' => $images, 'upsale' => $up_sale]);
    }
}
