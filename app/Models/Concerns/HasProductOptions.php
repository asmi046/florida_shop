<?php

namespace App\Models\Concerns;

use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasProductOptions
{
    /**
     * Все значения параметров товара.
     */
    public function productOptions(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_product_option',
            'product_id',
            'product_option_id'
        )->withPivot(['quantity', 'sort']);
    }

    /**
     * Значения параметров товара одного типа.
     */
    public function optionsOfType(string $typeSlug): BelongsToMany
    {
        return $this->productOptions()
            ->whereHas('type', fn (Builder $query) => $query->where('slug', $typeSlug));
    }
}
