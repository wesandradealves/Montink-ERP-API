<?php

namespace App\Modules\Orders\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\DocumentFormatter;
use App\Common\Traits\UnifiedValidationMessages;

class CreateOrderRequest extends BaseFormRequest
{
    use UnifiedValidationMessages, DocumentFormatter;

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
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

    protected array $customMessages = [
        'customer_cpf.size' => 'O CPF deve ter 14 caracteres (incluindo pontos e traço)',
        'customer_cep.size' => 'O CEP deve ter 9 caracteres (incluindo traço)',
        'customer_state.size' => 'O estado deve ter 2 caracteres (ex: SP)',
    ];

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