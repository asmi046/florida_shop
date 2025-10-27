<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductTag;

class ProductTagInFilter extends Component
{
    /**
     * Теги продуктов для фильтра
     */
    public $tags;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Загружаем теги с кешированием
        $this->tags = $this->getTagsWithCache();
    }

    /**
     * Получить теги с кешированием
     */
    private function getTagsWithCache()
    {
        return Cache::remember('product_tags_filter', 3600, function () {
            return ProductTag::select('id', 'title', 'alt_title', 'slug')
                ->orderBy('title')
                ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-tag-in-filter');
    }
}
