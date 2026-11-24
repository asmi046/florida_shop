<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\MySkladTechPlanAvailabilityService;
use Illuminate\Http\Request;
use RuntimeException;

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
                $query->where('title', 'like', '%'.$search.'%');
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

    // Детализация состава букета: что есть на складе и сколько букетов можно собрать
    public function product(Product $product, MySkladTechPlanAvailabilityService $service)
    {
        if (empty($product->processingPlanId)) {
            return response()->view('dashboard.product', [
                'product' => $product,
                'data' => null,
                'error' => 'У товара не заполнен ID техкарты — детализация недоступна.',
            ], 404);
        }

        try {
            $data = $service->build($product);
        } catch (RuntimeException $e) {
            return response()->view('dashboard.product', [
                'product' => $product,
                'data' => null,
                'error' => 'Не удалось получить данные из МойСклад: '.$e->getMessage(),
            ], 503);
        }

        return view('dashboard.product', [
            'product' => $product,
            'data' => $data,
            'error' => null,
        ]);
    }
}
