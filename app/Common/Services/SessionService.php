<?php

namespace App\Common\Services;

use Illuminate\Support\Facades\Session;

class SessionService
{
    public static function getCurrentId(): string
    {
        // Para API, sempre usar cookie session_id
        $sessionId = request()->cookie('session_id');
        
        if (!$sessionId) {
            // Se não houver cookie, verificar se há sessão Laravel
            if (Session::getId()) {
                $sessionId = Session::getId();
            } else {
                // Gerar novo ID único
                $sessionId = uniqid('cart_', true);
            }
        }
        
        return $sessionId;
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