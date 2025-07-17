<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Montink ERP API',
        'version' => '0.1.0',
        'status' => 'running'
    ]);
});