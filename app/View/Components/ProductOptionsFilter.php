<?php

namespace App\View\Components;

use App\Support\ProductOptionCatalog;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductOptionsFilter extends Component
{
    /**
     * Тип параметра: слаг из справочника типов.
     */
    public string $dimension;

    /**
     * Подпись типа параметра в фильтре.
     */
    public string $title;

    public function __construct(string $dimension, string $title)
    {
        $this->dimension = $dimension;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-options-filter', [
            'values' => ProductOptionCatalog::options($this->dimension),
        ]);
    }
}
