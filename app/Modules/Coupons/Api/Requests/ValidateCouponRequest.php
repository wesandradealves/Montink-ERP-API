<?php

namespace App\Modules\Coupons\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\ValidationMessagesTrait;

class ValidateCouponRequest extends BaseFormRequest
{
    use ValidationMessagesTrait;

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50',
            'value' => 'required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return array_merge($this->getCommonValidationMessages(), [
            'value.min' => 'O valor deve ser maior ou igual a zero'
        ]);
    }
}