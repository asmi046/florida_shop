<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;

class ProductOptionType extends Model
{
    use AsSource;
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'position',
        'show_in_filter',
    ];

    protected $casts = [
        'position' => 'integer',
        'show_in_filter' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)
            ->orderBy('position')
            ->orderBy('title');
    }

    /**
     * Типы, которые показываются в фильтре каталога.
     */
    public function scopeFilterable(Builder $query): void
    {
        $query->where('show_in_filter', true)
            ->orderBy('position');
    }
}
