<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function catalog(ProductFilter $request)
    {
        $allproduct = Product::select('*')->filter($request)->orderBy('created_at', 'DESC')->paginate(16)->withQueryString();

        if ($allproduct->currentPage() > $allproduct->lastPage() && $allproduct->total() > 0) {
            abort(404);
        }

        return view('catalog', ['allproduct' => $allproduct]);
    }

    public function show_cat($slug)
    {
        $categoryInfo = Category::where('slug', $slug)->first();

        if ($categoryInfo == null) {
            abort(404);
        }

        $allproduct = $categoryInfo->category_tovars()->paginate(16)->withQueryString();

        if ($allproduct->currentPage() > $allproduct->lastPage() && $allproduct->total() > 0) {
            abort(404);
        }

        return view('category', ['cat_info' => $categoryInfo, 'allproduct' => $allproduct]);
    }
}
