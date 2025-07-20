<?php

namespace App\Common\Exceptions;

use Exception;

class TokenException extends Exception
{
    public static function invalidToken(): self
    {
        return new self('Token inválido', 401);
    }

    public static function expiredToken(): self
    {
        return new self('Token expirado', 401);
    }

    public static function invalidSignature(): self
    {
        return new self('Assinatura do token inválida', 401);
    }
}