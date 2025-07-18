<?php

namespace App\Modules\Coupons\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;
use App\Modules\Coupons\Models\Coupon;

class CreateCouponRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Coupon::getTypes())),
            'value' => 'required|numeric|min:0.01',
            'minimum_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date|date_format:Y-m-d',
            'valid_until' => 'nullable|date|date_format:Y-m-d|after_or_equal:valid_from',
            'active' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return array_merge($this->getDefaultValidationMessages(), [
            'code.unique' => 'Já existe um cupom com este código',
            'type.in' => 'O tipo de cupom deve ser: fixed (valor fixo) ou percentage (porcentagem)',
            'value.min' => 'O valor do cupom deve ser maior que zero',
            'usage_limit.min' => 'O limite de uso deve ser no mínimo 1',
            'valid_until.after_or_equal' => 'A data de validade deve ser posterior ou igual à data de início'
        ]);
    }
}