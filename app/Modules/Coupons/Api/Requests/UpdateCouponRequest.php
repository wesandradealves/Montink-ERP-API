<?php

namespace App\Modules\Coupons\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;
use App\Modules\Coupons\Models\Coupon;

class UpdateCouponRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'description' => 'sometimes|nullable|string|max:255',
            'type' => 'sometimes|string|in:' . implode(',', array_keys(Coupon::getTypes())),
            'value' => 'sometimes|numeric|min:0.01',
            'minimum_value' => 'sometimes|nullable|numeric|min:0',
            'usage_limit' => 'sometimes|nullable|integer|min:1',
            'valid_from' => 'sometimes|nullable|date|date_format:Y-m-d',
            'valid_until' => 'sometimes|nullable|date|date_format:Y-m-d',
            'active' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return array_merge($this->getDefaultValidationMessages(), [
            'type.in' => 'O tipo de cupom deve ser: fixed (valor fixo) ou percentage (porcentagem)',
            'value.min' => 'O valor do cupom deve ser maior que zero',
            'usage_limit.min' => 'O limite de uso deve ser no mínimo 1'
        ]);
    }
}