<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Product extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable;

    public $fillable = [
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
        'height',
        'radius',
        'seo_title',
        'seo_description'
    ];

    protected $allowedSorts = [
        'id',
        'sku',
        'title'
    ];


    public function scopeFilter(Builder $builder, QueryFilter $filter) {
        return $filter->apply($builder);
    }

    public function setSlugAttribute($value)
    {
        if (empty($value))
            $this->attributes['slug'] =  Str::slug($this->title);
        else
            $this->attributes['slug'] =  $value;
    }

    public function product_images() {
        return $this->hasMany(ProductImage::class);
    }

    public function tovar_categories() {
        // return  $this->hasOne(Category::class, "title", "category");
        return $this->belongsToMany(Category::class);
    }
}
