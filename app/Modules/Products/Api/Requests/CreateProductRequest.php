<?php

namespace App\Modules\Products\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;

class CreateProductRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'active' => ['required', 'boolean'],
            'variations' => ['nullable', 'array'],
        ];
    }

    protected array $customMessages = [
        'sku.unique' => 'Este SKU já está em uso',
        'price.min' => 'O preço deve ser maior que zero',
    ];
}