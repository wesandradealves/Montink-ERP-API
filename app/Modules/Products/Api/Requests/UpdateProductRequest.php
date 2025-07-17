<?php

namespace App\Modules\Products\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => ['sometimes', 'string', 'max:100', "unique:products,sku,{$productId}"],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'active' => ['boolean'],
            'variations' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser uma string',
            'sku.unique' => 'Este SKU já está em uso',
            'price.numeric' => 'O preço deve ser um número',
            'price.min' => 'O preço deve ser maior que zero',
        ];
    }
}