<?php

namespace App\Modules\Products\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\ValidationMessagesTrait;

class UpdateProductRequest extends BaseFormRequest
{
    use ValidationMessagesTrait;

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

    protected array $customMessages = [
        'sku.unique' => 'Este SKU já está em uso',
        'price.min' => 'O preço deve ser maior que zero',
    ];
}