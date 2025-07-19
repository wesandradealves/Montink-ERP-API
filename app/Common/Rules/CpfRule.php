<?php

namespace App\Common\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Common\Enums\ResponseMessage;

class CpfRule implements Rule
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
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1*$/', $cpf)) {
            return false;
        }
        
        // Valida primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((10 - $i) * intval($cpf[$i]));
        }
        $firstDigit = 11 - ($sum % 11);
        if ($firstDigit >= 10) {
            $firstDigit = 0;
        }
        if (intval($cpf[9]) != $firstDigit) {
            return false;
        }
        
        // Valida segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += ((11 - $i) * intval($cpf[$i]));
        }
        $secondDigit = 11 - ($sum % 11);
        if ($secondDigit >= 10) {
            $secondDigit = 0;
        }
        if (intval($cpf[10]) != $secondDigit) {
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
        return ResponseMessage::VALIDATION_CPF_FORMAT->get();
    }
}