<?php

namespace App\Common\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuantityRule implements Rule
{
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $value > 0;
    }

    public function message()
    {
        return 'A quantidade deve ser maior que zero';
    }
}