<?php

namespace App\Modules\Cart\Providers;

use App\Modules\Cart\UseCases\CartUseCase;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartUseCase::class, CartUseCase::class);
        $this->app->bind(\App\Modules\Cart\Services\ShippingService::class, \App\Modules\Cart\Services\ShippingService::class);
    }

    public function boot(): void
    {
        //
    }
}