<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Orchid\Screen\AsSource;

class BlogPost extends Model
{
    use HasFactory;
    use AsSource;

    public $fillable = [
        'created_at',
        'title',
        'slug',
        'img',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value))
            $this->attributes['slug'] =  Str::slug($this->title);
        else
            $this->attributes['slug'] =  $value;
    }

}
