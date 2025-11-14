<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\MextFoodController;

Route::post('/register', [UserController::class, 'register'])->middleware(['throttle:6,1']);
Route::post('/login', [UserController::class, 'login'])->middleware(['throttle:6,1']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::prefix('email')->group(function () {
    Route::post('/verification-notification', [UserController::class, 'verificationNotification'])
       ->middleware(['auth:sanctum', 'throttle:6,1']);
    Route::get('/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])
       ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])->name('verification.verify');
});

Route::prefix('user')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});

Route::prefix('diary')->middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/{date}', [DiaryController::class, 'index'])->where('date', '[0-9]{8}');
    Route::put('/{date}', [DiaryController::class, 'store'])->where('date', '[0-9]{8}');
});

Route::apiResource('foods', FoodController::class)->whereNumber('food')
    ->middleware('auth:sanctum');
Route::apiResource('recipes', RecipeController::class)->whereNumber('recipe')
    ->middleware('auth:sanctum');

Route::post('/mext-food/search', [MextFoodController::class, 'search'])
    ->middleware('auth:sanctum');

