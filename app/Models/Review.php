<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Attachment\Attachable;

use Orchid\Screen\AsSource;

class Review extends Model
{
    use HasFactory;
    use AsSource;
    use Attachable;

    public $fillable = [
        'slug',
        'name',
        'lnk',
        'avatar',
        'text'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(
            function (Review $review) {
                $review->slug = $review->slug ?? str($review->name)->slug();
            }
        );
    }
}
