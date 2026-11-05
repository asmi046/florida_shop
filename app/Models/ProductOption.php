<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ProductOption extends Model
{
    use AsSource;
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'product_option_type_id',
        'title',
        'slug',
        'image',
        'external_id',
        'meta',
        'position',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductOptionType::class, 'product_option_type_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_product_option',
            'product_option_id',
            'product_id'
        )->withPivot(['quantity', 'sort']);
    }

    /**
     * Значения определённого типа, по слагу типа.
     */
    public function scopeOfType(Builder $query, string $typeSlug): void
    {
        $query->whereHas('type', fn (Builder $query) => $query->where('slug', $typeSlug));
    }

    /**
     * Значения, доступные в фильтре.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function setSlugAttribute($value): void
    {
        $slug = $value !== null && $value !== '' ? $value : ($this->attributes['title'] ?? '');

        $this->attributes['slug'] = Str::slug($slug);
    }
}
