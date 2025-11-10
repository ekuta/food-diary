<?php
namespace App\Enums;

enum MealType: int
{
    case Breakfirst = 1;
    case Lunch      = 2;
    case Dinner     = 3;
    case Snack      = 4;
}
