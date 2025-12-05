<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regular extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'servings' => 'float',
        'amount' => 'float',
        'calory' => 'float',
        'protein' => 'float',
        'fat' => 'float',
        'carbs' => 'float',
        'salt' => 'float',
    ];
}
