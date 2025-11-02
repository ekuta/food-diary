<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DiaryController;
//use App\Http\Controllers\FoodController;
use App\Http\Controllers\RecipeController;

Route::prefix('user')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
});

Route::get('/diary/{date}', [DiaryController::class, 'index'])->where('date', '[0-9]{8}')
    ->middleware('auth:sanctum');

//Route::apiResource('foods', FoodController::class)->where(['food' => '[0-9]+']);
Route::apiResource('recipes', RecipeController::class)->whereNumber('recipe')
    ->middleware('auth:sanctum');
