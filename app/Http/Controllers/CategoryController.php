<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;

use App\Filters\ProductFilter;

class CategoryController extends Controller
{

    public function catalog(ProductFilter $request) {
        $allproduct = Product::select("*")->filter($request)->paginate(9)->withQueryString();

        return view('catalog', [ 'allproduct' => $allproduct]);
    }

    public function show_cat ($slug) {
        $categoryInfo = Category::where('slug', $slug)->first();

        if($categoryInfo == null) abort('404');

        $allproduct = $categoryInfo->category_tovars;

        return view('category', ['cat_info' => $categoryInfo, 'allproduct' => $allproduct]);
    }
}
