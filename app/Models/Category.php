<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use AsSource;
    use HasFactory;

    public $fillable = [
        'title',
        'slug',
        'in_main',
        'showed_title',
        'description',
        'img',
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }

    public function category_tovars()
    {
        return $this->belongsToMany(Product::class);
    }
}
