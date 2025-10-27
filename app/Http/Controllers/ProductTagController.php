<?php

namespace App\Http\Controllers;

use App\Models\ProductTag;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function show_tag ($slug) {
        $tagInfo = ProductTag::where('slug', $slug)->first();

        if($tagInfo == null) abort('404');

        $allproduct = $tagInfo->products;

        return view('tag', ['tag_info' => $tagInfo, 'allproduct' => $allproduct]);
    }
}
