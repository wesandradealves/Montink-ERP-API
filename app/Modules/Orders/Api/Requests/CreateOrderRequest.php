<?php

namespace App\Modules\Orders\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\ValidationMessagesTrait;

class CreateOrderRequest extends BaseFormRequest
{
    use ValidationMessagesTrait;

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
        'customer_name.required' => 'O nome do cliente é obrigatório',
        'customer_email.required' => 'O email do cliente é obrigatório',
        'customer_email.email' => 'O email deve ser válido',
        'customer_cpf.size' => 'O CPF deve ter 14 caracteres (incluindo pontos e traço)',
        'customer_cep.required' => 'O CEP é obrigatório',
        'customer_cep.size' => 'O CEP deve ter 9 caracteres (incluindo traço)',
        'customer_address.required' => 'O endereço é obrigatório',
        'customer_neighborhood.required' => 'O bairro é obrigatório',
        'customer_city.required' => 'A cidade é obrigatória',
        'customer_state.required' => 'O estado é obrigatório',
        'customer_state.size' => 'O estado deve ter 2 caracteres (ex: SP)',
    ];

    public function prepareForValidation(): void
    {
        if ($this->has('customer_cep')) {
            $cep = preg_replace('/[^0-9]/', '', $this->customer_cep);
            if (strlen($cep) === 8) {
                $this->merge([
                    'customer_cep' => substr($cep, 0, 5) . '-' . substr($cep, 5, 3)
                ]);
            }
        }

        if ($this->has('customer_cpf')) {
            $cpf = preg_replace('/[^0-9]/', '', $this->customer_cpf);
            if (strlen($cpf) === 11) {
                $this->merge([
                    'customer_cpf' => substr($cpf, 0, 3) . '.' . 
                                     substr($cpf, 3, 3) . '.' . 
                                     substr($cpf, 6, 3) . '-' . 
                                     substr($cpf, 9, 2)
                ]);
            }
        }
    }
}