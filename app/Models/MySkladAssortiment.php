<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MySkladAssortiment extends Model
{
    protected $fillable = [
        'sklad_id',
        'type',
        'name',
        'code',
        'externalCode',
        'pathName',
        'components_href',
        'components_size',
    ];

    protected $casts = [
        'components_size' => 'integer',
    ];
}
