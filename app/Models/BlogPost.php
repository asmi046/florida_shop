<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Orchid\Screen\AsSource;

class BlogPost extends Model
{
    use AsSource;
    use HasFactory;

    public $fillable = [
        'title',
        'slug',
        'short_description',
        'published_at',
        'img',
        'description',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }
}
