<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

Route::apiResource('products', \App\Modules\Products\Api\Controllers\ProductController::class);

Route::prefix('cart')->group(function () {
    Route::get('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'index']);
    Route::post('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'store']);
    Route::put('/{id}', [\App\Modules\Cart\Api\Controllers\CartController::class, 'update']);
    Route::delete('/{id}', [\App\Modules\Cart\Api\Controllers\CartController::class, 'destroy']);
    Route::delete('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'clear']);
});

Route::get('/', function () {
    return redirect('/docs');
});