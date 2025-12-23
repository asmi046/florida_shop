<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function show() {

        $all_product = Product::paginate(9);
        $sales_liders = Product::select()->orderBy('sales_count')->take(8)->get();
        $sales = Product::where('old_price', '!=', 0)->orWhere("hit", 1)->orderBy("created_at", "DESC")->take(8)->get();

        $news = Product::where('new', true)->take(8)->get();
        $hits = Product::where('hit', true)->take(8)->get();

        $reviews = Review::select()->take(9)->get();

        if (count($sales) == 0)
            $sales = Product::where('hit', true)->take(8)->get();

        return view('index', [
            'all_product' => $all_product,
            'sales_liders' => $sales_liders,
            'sales' => $sales,
            'news' => $news,
            'hits' => $hits,
            'reviews' => $reviews,
        ]);
    }
}
