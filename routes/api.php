<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RegularController;
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

Route::apiResource('diary', DiaryController::class)->whereNumber('diary')
    ->middleware('auth:sanctum');
Route::get('/diary/history', [DiaryController::class, 'history'])
    ->middleware('auth:sanctum');

Route::apiResource('food', FoodController::class)->whereNumber('food')
    ->except(['index'])->middleware('auth:sanctum');
Route::put('/food/search', [FoodController::class, 'search'])
    ->middleware('auth:sanctum');
Route::apiResource('recipe', RegularController::class)->whereNumber('recipe')
    ->middleware('auth:sanctum');

Route::put('/mext-food/search', [MextFoodController::class, 'search'])
    ->middleware('auth:sanctum');
Route::get('/mext-food/{id}', [MextFoodController::class, 'show'])
    ->whereNumber('id')->middleware('auth:sanctum');

