<?php

namespace App\Modules\Orders\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\DocumentFormatter;
use App\Common\Traits\UnifiedValidationMessages;
use App\Common\Traits\EmailValidationTrait;

class CreateOrderRequest extends BaseFormRequest
{
    use UnifiedValidationMessages, DocumentFormatter, EmailValidationTrait;

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => array_merge(['required'], $this->emailRules(), ['max:255']),
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_cpf' => ['nullable', 'string', 'size:14'],
            'customer_cep' => ['required', 'string', 'size:9'],
            'customer_address' => ['required', 'string', 'max:255'],
            'customer_complement' => ['nullable', 'string', 'max:255'],
            'customer_neighborhood' => ['required', 'string', 'max:255'],
            'customer_city' => ['required', 'string', 'max:255'],
            'customer_state' => ['required', 'string', 'size:2'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected array $customMessages = [];

    public function prepareForValidation(): void
    {
        if ($this->has('customer_cep')) {
            $this->merge([
                'customer_cep' => $this->formatCep($this->customer_cep)
            ]);
        }

        if ($this->has('customer_cpf')) {
            $this->merge([
                'customer_cpf' => $this->formatCpf($this->customer_cpf)
            ]);
        }
    }
}