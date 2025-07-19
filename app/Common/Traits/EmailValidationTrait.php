<?php

namespace App\Common\Traits;

trait EmailValidationTrait
{
    /**
     * Regras de validação para email
     */
    protected function emailRules(): array
    {
        return ['required', 'email', 'max:255'];
    }

    /**
     * Regras de validação para email opcional
     */
    protected function optionalEmailRules(): array
    {
        return ['nullable', 'email', 'max:255'];
    }

    /**
     * Regras de validação para email único
     */
    protected function uniqueEmailRules(string $table = 'users', ?int $ignoreId = null): array
    {
        $rules = $this->emailRules();
        
        if ($ignoreId) {
            $rules[] = "unique:{$table},email,{$ignoreId}";
        } else {
            $rules[] = "unique:{$table},email";
        }
        
        return $rules;
    }

    /**
     * Validar formato de email
     */
    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Normalizar email (lowercase, trim)
     */
    protected function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}