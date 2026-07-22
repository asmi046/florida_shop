<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MySkladStock extends Model
{
    protected $fillable = [
        'assortmentId',
        'freeStock',
    ];

    protected $casts = [
        'freeStock' => 'integer',
    ];
}
