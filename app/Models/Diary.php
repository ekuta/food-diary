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
    ];

    protected $casts = [
        'meal_type' => MealType::class,
    ];
}
