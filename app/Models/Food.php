<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\FoodType;

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
        'id' => 'integer',
        'user_id' => 'integer',
        'food_type' => FoodType::class,
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
