<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [
        'web' => [],
        'api' => [],
    ];

    protected $middlewareAliases = [
        'jwt.auth' => \App\Modules\Auth\Api\Middleware\JwtAuthMiddleware::class,
    ];
}