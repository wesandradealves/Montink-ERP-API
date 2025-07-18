<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

Route::apiResource('products', \App\Modules\Products\Api\Controllers\ProductController::class);

Route::get('/', function () {
    return redirect('/docs');
});