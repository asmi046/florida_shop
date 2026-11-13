<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Главная страница дашборда: таблица товаров с поиском и сортировкой
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'title');
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $search = trim((string) $request->query('search', ''));

        if (! in_array($sort, ['title', 'skladCount'], true)) {
            $sort = 'title';
        }

        $products = Product::query()
            ->select('id', 'title', 'skladCount', 'processingPlanId')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->orderBy($sort, $direction)
            ->orderBy('id') // стабильный порядок при совпадении значений
            ->get();

        return view('dashboard.index', [
            'products' => $products,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
