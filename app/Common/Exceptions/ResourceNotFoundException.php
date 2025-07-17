<?php

namespace App\Common\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    protected $code = 404;

    public function __construct(string $resource = 'Recurso', ?string $identifier = null)
    {
        $message = $identifier 
            ? "{$resource} com identificador '{$identifier}' não encontrado" 
            : "{$resource} não encontrado";
            
        parent::__construct($message, $this->code);
    }
}