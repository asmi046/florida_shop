<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    public $fillable = [
        'id',
        'sku',
        'title',
        'slug',
        'img',
        'description',
        'price',
        'old_price',
        'sales_count',
        'hit',
        'new',
        'asc_nal',
        'height',
        'radius',
        'consist',
        'seo_title',
        'seo_description',
        'skladCount',
        'code',
        'externalCode',
    ];

    protected $casts = [
        'consist' => 'array',
        'skladCount' => 'integer',
    ];

    protected $allowedSorts = [
        'id',
        'sku',
        'title',
        'skladCount',
    ];

    protected $allowedFilters = [
        'title' => Like::class,
    ];

    public $with = ['tovar_categories', 'product_images'];

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function tovar_categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tovar_celebration()
    {
        return $this->belongsToMany(Celebration::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class);
    }
}
