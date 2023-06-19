<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Screen\AsSource;
use Illuminate\Support\Str;

class Celebration extends Model
{
    use HasFactory;
    use AsSource;

    public $fillable = [
        'title',
        'slug',
        'description'
    ];

    public function setSlugAttribute($value)
    {
        if (empty($value))
            $this->attributes['slug'] =  Str::slug($this->title);
        else
            $this->attributes['slug'] =  $value;
    }

    public function celebration_tovars() {
        return $this->belongsToMany(Product::class);
    }
}
