<?php

namespace App\Modules\Cart\Api\Requests;

use App\Common\Rules\QuantityRule;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', new QuantityRule],
            'variations' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O produto é obrigatório',
            'product_id.exists' => 'Produto não encontrado',
            'quantity.required' => 'A quantidade é obrigatória',
        ];
    }
}