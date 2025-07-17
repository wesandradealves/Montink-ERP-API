<?php

namespace App\Common\Services;

use Illuminate\Support\Facades\Session;

class SessionService
{
    public static function getCurrentId(): string
    {
        return Session::getId();
    }

    public static function regenerateId(): void
    {
        Session::regenerate();
    }

    public static function flush(): void
    {
        Session::flush();
    }
}