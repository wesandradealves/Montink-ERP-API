<?php

namespace App\Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

class OrdersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \App\Modules\Orders\UseCases\OrdersUseCase::class,
            function ($app) {
                return new \App\Modules\Orders\UseCases\OrdersUseCase(
                    $app->make(\App\Modules\Cart\Services\ShippingService::class),
                    $app->make(\App\Modules\Coupons\UseCases\CouponsUseCase::class)
                );
            }
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations');
    }
}