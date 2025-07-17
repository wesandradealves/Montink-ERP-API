<?php

namespace App\Common\Traits;

trait ValidationMessagesTrait
{
    protected function getCommonValidationMessages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'numeric' => 'O campo :attribute deve ser um número',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'min' => 'O campo :attribute deve ser no mínimo :min',
            'max' => 'O campo :attribute não pode ser maior que :max',
            'unique' => 'Este :attribute já está em uso',
            'exists' => ':Attribute não encontrado',
            'email' => 'O campo :attribute deve ser um email válido',
            'email.email' => 'O email deve ser válido',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso',
            'array' => 'O campo :attribute deve ser uma lista',
            'size' => 'O campo :attribute deve ter :size caracteres',
            'date' => 'O campo :attribute deve ser uma data válida',
            'date_format' => 'O campo :attribute deve estar no formato :format',
            'in' => 'O campo :attribute selecionado é inválido',
            'decimal' => 'O campo :attribute deve ter :decimal casas decimais',
        ];
    }

    protected function getFieldAttributes(): array
    {
        return [
            'name' => 'nome',
            'price' => 'preço',
            'quantity' => 'quantidade',
            'product_id' => 'produto',
            'sku' => 'SKU',
            'description' => 'descrição',
            'email' => 'e-mail',
            'password' => 'senha',
            'cep' => 'CEP',
            'customer_name' => 'nome do cliente',
            'customer_email' => 'email do cliente',
            'customer_phone' => 'telefone do cliente',
            'customer_cpf' => 'CPF do cliente',
            'customer_cep' => 'CEP do cliente',
            'customer_address' => 'endereço do cliente',
            'customer_complement' => 'complemento',
            'customer_neighborhood' => 'bairro',
            'customer_city' => 'cidade',
            'customer_state' => 'estado',
            'code' => 'código',
            'type' => 'tipo',
            'value' => 'valor',
            'minimum_value' => 'valor mínimo',
            'usage_limit' => 'limite de uso',
            'valid_from' => 'válido a partir de',
            'valid_until' => 'válido até',
            'active' => 'ativo',
            'status' => 'status',
            'variations' => 'variações',
        ];
    }

    public function messages(): array
    {
        return array_merge($this->getCommonValidationMessages(), $this->customMessages ?? []);
    }

    public function attributes(): array
    {
        return array_merge($this->getFieldAttributes(), $this->customAttributes ?? []);
    }
}