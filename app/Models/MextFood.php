<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MextFood extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'calory' => 'integer',
        'protein' => 'float',
        'fat' => 'float',
        'carbs' => 'float',
        'salt' => 'float',
    ];
}
