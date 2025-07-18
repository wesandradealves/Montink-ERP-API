<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '0.1.0')
    ]);
});

Route::middleware('throttle:api')->group(function () {
    // API routes will be added here
});