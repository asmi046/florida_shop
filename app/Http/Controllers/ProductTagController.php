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

        $allproduct = $tagInfo->products()->filter($request)->orderBy('asc_nal', 'ASC')->paginate(9)->withQueryString();

        if ($allproduct->currentPage() > $allproduct->lastPage() && $allproduct->total() > 0) {
            abort(404);
        }

        if (request()->ajax()) {
            return response()->json([
                'html' => view('partials.product-list', ['allproduct' => $allproduct])->render(),
                'count' => $allproduct->total(),
            ]);
        }

        return view('tag', ['tag_info' => $tagInfo, 'allproduct' => $allproduct]);
    }
}
