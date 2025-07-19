<?php

namespace App\Modules\Cart\Providers;

use App\Modules\Cart\UseCases\CartUseCase;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartUseCase::class, function ($app) {
            return new CartUseCase(
                $app->make(\App\Modules\Cart\Services\ShippingService::class),
                $app->make(\App\Modules\Stock\Services\StockValidationService::class),
                $app->make(\App\Common\Services\SessionService::class)
            );
        });
        $this->app->bind(\App\Modules\Cart\Services\ShippingService::class, \App\Modules\Cart\Services\ShippingService::class);
    }

    public function boot(): void
    {
        //
    }
}