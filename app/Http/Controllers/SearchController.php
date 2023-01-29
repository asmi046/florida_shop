<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{

    public function show_search_page(Request $request) {
        $search_str = $request->get('search_str');

        $tovars = Product::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->orWhere('sku', 'LIKE', "%".$search_str."%")
        ->get();

        return view('search', ['tovars' => $tovars, 'search_str' => $search_str]);
    }

    public function search_pds(Request $request) {
        $result_array = ["products" => [], "categories" => [], "img_prefix" => Storage::url('')];

        $search_str = $request->get('search_str');

        if (empty($search_str)) return $result_array;

        $products = Product::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->orWhere('sku', 'LIKE', "%".$search_str."%")
        ->take(30)
        ->get();

        $cat = Category::where('title', 'LIKE', "%".$search_str."%")
        ->orWhere('description', 'LIKE', "%".$search_str."%")
        ->take(5)
        ->get();

        $result_array["categories"] = $cat;

        $result_array["products"] = $products;

        return $result_array;
    }
}
