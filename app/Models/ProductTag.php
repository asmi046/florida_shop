<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ProductTag extends Model
{
    use AsSource;
    use Filterable;

    /**
     * Ключ кэша списка тегов для фильтра.
     */
    public const FILTER_CACHE_KEY = 'product_tags_filter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'alt_title',
        'template',
        'image',
        'slug',
        'description',
        'seo_title',
        'seo_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Сбросить кэш списка тегов для фильтра.
     */
    public static function clearFilterCache(): void
    {
        Cache::forget(self::FILTER_CACHE_KEY);
    }
}
