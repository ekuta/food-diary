<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'search_string',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'food_type' => 'integer',
        'food_amount' => 'integer',
        'calory' => 'integer',
        'protein' => 'float',
        'fat' => 'float',
        'carbs' => 'float',
        'salt' => 'float',
        'usage_type' => 'integer',
        'gram_per_food_unit' => 'float',
        'amount_per_tablespoon' => 'float',
        'ml_per_food_unit' => 'float',
        'amount_per_package' => 'float',
    ];
}
