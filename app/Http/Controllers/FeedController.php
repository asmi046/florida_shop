<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Category;
use App\Models\Product;


class FeedController extends Controller
{
    public function yml_actegory($slug) {
        if ($slug === 'all')
        {
                $catProducts = Product::all();
                $categoryInfo = null;
                // dd($catProducts);
        }
        else
        {
            $categoryInfo = Category::where('slug', $slug)->first();
            if($categoryInfo->isDirty()) abort('404');
            $catProducts = $categoryInfo->category_tovars()->get();
        }




        $all_categories = Category::all();

        return response()->view('feed.yml_cactegory', ["category" => $categoryInfo, "all_cat"=> $all_categories,  "cat_product" => $catProducts])->header('Content-Type', 'text/xml');;
    }
}
