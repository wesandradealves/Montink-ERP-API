<?php

namespace App\Common\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Common\Enums\ResponseMessage;

class CepRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $value);
        
        // Verifica se tem 8 dígitos
        if (strlen($cep) != 8) {
            return false;
        }
        
        // Verifica se não é uma sequência inválida conhecida
        $invalidCeps = [
            '00000000',
            '11111111',
            '22222222',
            '33333333',
            '44444444',
            '55555555',
            '66666666',
            '77777777',
            '88888888',
            '99999999'
        ];
        
        if (in_array($cep, $invalidCeps)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return ResponseMessage::VALIDATION_CEP_FORMAT->get();
    }
}