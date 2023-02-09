<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;

class BlogPost extends Model
{
    use HasFactory;
    use AsSource;

    public $fillable = [
        'title',
        'slug',
        'img',
        'description',
        'seo_title',
        'seo_description',
    ];
}
