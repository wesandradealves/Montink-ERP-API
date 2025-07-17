<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return ['status' => 'healthy', 'timestamp' => date('c'), 'version' => '0.1.0'];
});

Route::apiResource('products', \App\Modules\Products\Api\Controllers\ProductController::class);