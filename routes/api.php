<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Modules\Auth\Api\Controllers\AuthController::class, 'login']);
    Route::post('/register', [\App\Modules\Auth\Api\Controllers\AuthController::class, 'register']);
    Route::post('/refresh', [\App\Modules\Auth\Api\Controllers\AuthController::class, 'refresh']);
    
    Route::middleware('jwt.auth')->group(function () {
        Route::post('/logout', [\App\Modules\Auth\Api\Controllers\AuthController::class, 'logout']);
        Route::get('/me', [\App\Modules\Auth\Api\Controllers\AuthController::class, 'me']);
    });
});

Route::apiResource('products', \App\Modules\Products\Api\Controllers\ProductController::class);

Route::prefix('cart')->group(function () {
    Route::get('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'index']);
    Route::post('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'store']);
    Route::patch('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'update']);
    Route::patch('/{id}', [\App\Modules\Cart\Api\Controllers\CartController::class, 'update']);
    Route::delete('/{id}', [\App\Modules\Cart\Api\Controllers\CartController::class, 'destroy']);
    Route::delete('/', [\App\Modules\Cart\Api\Controllers\CartController::class, 'clear']);
    Route::post('/coupon', [\App\Modules\Cart\Api\Controllers\CartController::class, 'applyCoupon']);
});

Route::prefix('address')->group(function () {
    Route::get('/cep/{cep}', [\App\Modules\Address\Api\Controllers\AddressController::class, 'getByCep']);
    Route::post('/validate-cep', [\App\Modules\Address\Api\Controllers\AddressController::class, 'validateCep']);
});

Route::prefix('orders')->group(function () {
    Route::get('/', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'index']);
    Route::post('/', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'store']);
    Route::get('/number/{orderNumber}', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'showByNumber']);
    Route::get('/{id}', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'show']);
    Route::patch('/{id}/status', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'updateStatus']);
    Route::delete('/{id}', [\App\Modules\Orders\Api\Controllers\OrderController::class, 'destroy']);
});

Route::prefix('coupons')->group(function () {
    Route::get('/', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'index']);
    Route::post('/', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'store']);
    Route::get('/{id}', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'show']);
    Route::get('/code/{code}', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'showByCode']);
    Route::patch('/{id}', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'update']);
    Route::delete('/{id}', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'destroy']);
    Route::post('/validate', [\App\Modules\Coupons\Api\Controllers\CouponController::class, 'validateCoupon']);
});

Route::prefix('webhooks')->group(function () {
    Route::post('/order-status', [\App\Modules\Webhooks\Api\Controllers\WebhookController::class, 'orderStatus']);
});

Route::get('/', function () {
    return redirect('/docs');
});