<?php

namespace App\Modules\Coupons\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;

class ValidateCouponRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50',
            'value' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return array_merge($this->getDefaultValidationMessages(), [
            'value.min' => 'O valor deve ser maior ou igual a zero'
        ]);
    }
}