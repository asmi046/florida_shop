<?php

namespace App\Filters;

use App\Models\ProductOptionType;
use App\Support\ProductOptionCatalog;
use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{
    /**
     * @var array<int, string>
     */
    protected array $allowed = [
        'minprice',
        'maxprice',
        'order',
        'osobennosti',
    ];

    public function minprice($minprice = 0)
    {
        if (! empty($minprice)) {
            $this->builder->where('price', '>=', $minprice);
        }
    }

    public function maxprice($maxprice = 0)
    {
        if (! empty($maxprice)) {
            $this->builder->where('price', '<', $maxprice);
        }
    }

    public function order($order)
    {
        if ($order == 'Сначала дешевые') {
            $this->builder->orderBy('price', 'asc');
        }
        if ($order == 'Сначала дорогие') {
            $this->builder->orderBy('price', 'desc');
        }
        if ($order == 'В алфавитном порядке') {
            $this->builder->orderBy('title', 'asc');
        }
    }

    /**
     * Фильтр по особостям товара.
     *
     * Внутри одной группы работает «ИЛИ»: товар подходит, если он обладает
     * хотя бы одной из отмеченных особенностей.
     */
    public function osobennosti($osobennosti = [])
    {
        $this->builder->where(function ($query) use ($osobennosti) {
            foreach ((array) $osobennosti as $osobennost) {
                if ($osobennost == 'Скидка') {
                    $query->orWhere('old_price', '>', 0);
                }
                if ($osobennost == 'Хит продаж') {
                    $query->orWhere('hit', 1);
                }
                if ($osobennost == 'Новинки') {
                    $query->orWhere('new', 1);
                }
            }
        });
    }

    /**
     * Фильтр по параметрам товара с множественным выбором.
     *
     * Внутри одного параметра работает «ИЛИ» (товар подходит, если обладает
     * хотя бы одним из выбранных значений), между разными параметрами — «И».
     * Значения приходят в виде ?color[]=1&color[]=2.
     */
    protected function applyExtra(Builder $builder): Builder
    {
        if (! ProductOptionCatalog::tablesExist()) {
            return $builder;
        }

        $typeIds = ProductOptionType::query()
            ->whereIn('slug', ProductDimensions::slugs())
            ->where('show_in_filter', true)
            ->pluck('id', 'slug');

        foreach ($typeIds as $slug => $typeId) {
            $ids = $this->dimensionIds($slug);

            if ($ids === []) {
                continue;
            }

            $builder->whereHas('productOptions', function (Builder $query) use ($typeId, $ids) {
                $query->where('product_options.product_option_type_id', $typeId)
                    ->whereIn('product_options.id', $ids);
            });
        }

        return $builder;
    }

    /**
     * Идентификаторы значений параметра из query-строки.
     *
     * @return array<int, int>
     */
    protected function dimensionIds(string $slug): array
    {
        $raw = $this->request->query($slug);

        if ($raw === null || $raw === '') {
            return [];
        }

        return collect((array) $raw)
            ->filter(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }
}
