<?php

namespace App\Http\Controllers;

use App\Models\ProductTag;
use Illuminate\Http\Request;
use App\Filters\ProductFilter;

class ProductTagController extends Controller
{
    public function show_tag (ProductFilter $request, $slug) {
        $tagInfo = ProductTag::where('slug', $slug)->first();

        if($tagInfo == null) abort('404');

        $allproduct = $tagInfo->products()->filter($request)->paginate(9)->withQueryString();

        return view('tag', ['tag_info' => $tagInfo, 'allproduct' => $allproduct]);
    }
}
