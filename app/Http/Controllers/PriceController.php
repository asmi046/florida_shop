<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Product;

class PriceController extends Controller
{
    public function show(ProductFilter $filter, $slug)
    {
        $ranges = config('prices', []);

        if (! isset($ranges[$slug])) {
            abort(404);
        }

        $range = $ranges[$slug];

        $min = $range['min'] ?? null;
        $max = $range['max'] ?? null;

        $query = Product::query()
            ->when(! is_null($min), fn ($query) => $query->where('price', '>=', $min))
            ->when(! is_null($max), fn ($query) => $query->where('price', '<', $max))
            ->filter($filter);

        if (! array_key_exists('order', $filter->filters())) {
            $query->orderBy('price', 'ASC')->orderBy('asc_nal', 'ASC');
        }

        $allproduct = $query->paginate(16)->withQueryString();

        if ($allproduct->currentPage() > $allproduct->lastPage() && $allproduct->total() > 0) {
            abort(404);
        }

        return view('price', [
            'slug' => $slug,
            'range' => $range,
            'allproduct' => $allproduct,
        ]);
    }
}
