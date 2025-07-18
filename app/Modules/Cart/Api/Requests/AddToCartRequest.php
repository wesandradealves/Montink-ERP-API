<?php

namespace App\Modules\Cart\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Rules\QuantityRule;
use App\Common\Traits\UnifiedValidationMessages;

class AddToCartRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', new QuantityRule],
            'variations' => ['nullable', 'array'],
        ];
    }

    protected array $customMessages = [];
}