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
                    $app->make(\App\Modules\Coupons\UseCases\CouponsUseCase::class),
                    $app->make(\App\Modules\Email\Services\EmailService::class),
                    $app->make(\App\Common\Services\SessionService::class),
                    $app->make(\App\Modules\Stock\Services\StockValidationService::class)
                );
            }
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../../../database/migrations');
    }
}