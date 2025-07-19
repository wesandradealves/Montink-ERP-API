<?php

namespace App\Modules\Email\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Email\Services\EmailService;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService(
                $app->make(\App\Modules\Email\Services\EmailTemplateService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../../../resources/views/emails', 'emails');
    }
}