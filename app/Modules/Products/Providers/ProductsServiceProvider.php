<?php

namespace App\Modules\Products\Providers;

use App\Infrastructure\Repositories\ProductRepository;
use App\Modules\Products\Repositories\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}