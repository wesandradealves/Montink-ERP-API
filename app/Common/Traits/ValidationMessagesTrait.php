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
            'exists' => 'O :attribute selecionado não existe',
            'email' => 'O campo :attribute deve ser um email válido',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso',
            'array' => 'O campo :attribute deve ser uma lista',
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