<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\MealType;

class Diary extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'user_id',
        'date',
        'meal_type',
        'recipe_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'meal_type' => MealType::class,
        'user_id' => 'integer',
        'recipe_id' => 'integer',
        'food_id' => 'integer',
        'servings' => 'float',
        'amount' => 'float',
        'calory' => 'float',
        'protein' => 'float',
        'fat' => 'float',
        'carbs' => 'float',
        'salt' => 'float',
    ];
}
