<?php

namespace App\Modules\Cart\Api\Requests;

use App\Common\Rules\QuantityRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', new QuantityRule],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'A quantidade é obrigatória',
        ];
    }
}