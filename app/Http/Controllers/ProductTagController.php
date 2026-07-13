<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\ProductTag;

class ProductTagController extends Controller
{
    public function show_tag(ProductFilter $request, $slug)
    {
        $tagInfo = ProductTag::where('slug', $slug)->first();

        if ($tagInfo == null) {
            abort('404');
        }

        $allproduct = $tagInfo->products()->filter($request)->paginate(9)->withQueryString();

        if ($allproduct->currentPage() > $allproduct->lastPage() && $allproduct->total() > 0) {
            abort(404);
        }

        return view('tag', ['tag_info' => $tagInfo, 'allproduct' => $allproduct]);
    }
}
