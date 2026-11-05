<?php

namespace App\Support;

use App\Filters\ProductDimensions;
use App\Models\ProductOption;
use App\Models\ProductOptionType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ProductOptionCatalog
{
    private const CACHE_TTL = 3600;

    /**
     * Существуют ли таблицы справочника.
     *
     * Нужна, чтобы сайт не падал с 500, если код задеплоили раньше миграции.
     */
    public static function tablesExist(): bool
    {
        return Cache::remember('product_options.tables_exist', self::CACHE_TTL, fn () => Schema::hasTable('product_option_types')
            && Schema::hasTable('product_options'));
    }

    /**
     * Типы параметров, которые показываются в фильтре каталога.
     */
    public static function types(): Collection
    {
        if (! self::tablesExist()) {
            return new Collection;
        }

        return Cache::remember('product_options.types', self::CACHE_TTL, function () {
            return ProductOptionType::query()
                ->filterable()
                ->get();
        });
    }

    /**
     * Значения параметра по слагу типа.
     */
    public static function options(string $typeSlug): Collection
    {
        if (! self::tablesExist()) {
            return new Collection;
        }

        return Cache::remember('product_options.values.'.$typeSlug, self::CACHE_TTL, function () use ($typeSlug) {
            return ProductOption::query()
                ->with('type')
                ->ofType($typeSlug)
                ->active()
                ->orderBy('position')
                ->orderBy('title')
                ->get();
        });
    }

    /**
     * Сбросить кэш справочника после изменений в админке.
     */
    public static function flush(): void
    {
        Cache::forget('product_options.tables_exist');
        Cache::forget('product_options.types');

        foreach (ProductDimensions::slugs() as $slug) {
            Cache::forget('product_options.values.'.$slug);
        }
    }
}
