<?php

namespace App\Common\Traits;

use App\Common\Enums\ResponseMessage;

trait UnifiedValidationMessages
{
    /**
     * Get validation messages
     */
    public function messages(): array
    {
        return array_merge($this->getDefaultValidationMessages(), $this->customMessages ?? []);
    }

    /**
     * Get attribute names
     */
    public function attributes(): array
    {
        return array_merge($this->getDefaultAttributes(), $this->customAttributes ?? []);
    }

    /**
     * Get default validation messages using ResponseMessage enum
     */
    protected function getDefaultValidationMessages(): array
    {
        return [
            'required' => ResponseMessage::VALIDATION_REQUIRED->get(),
            'string' => ResponseMessage::VALIDATION_STRING->get(),
            'numeric' => ResponseMessage::VALIDATION_NUMERIC->get(),
            'integer' => ResponseMessage::VALIDATION_INTEGER->get(),
            'email' => ResponseMessage::VALIDATION_EMAIL->get(),
            'min' => ResponseMessage::VALIDATION_MIN->get(),
            'max' => ResponseMessage::VALIDATION_MAX->get(),
            'unique' => ResponseMessage::VALIDATION_UNIQUE->get(),
            'exists' => ResponseMessage::VALIDATION_EXISTS->get(),
            'boolean' => ResponseMessage::VALIDATION_BOOLEAN->get(),
            'array' => ResponseMessage::VALIDATION_ARRAY->get(),
            'date' => ResponseMessage::VALIDATION_DATE->get(),
            'date_format' => ResponseMessage::VALIDATION_DATE_FORMAT->get(),
            'in' => ResponseMessage::VALIDATION_IN->get(),
            'decimal' => ResponseMessage::VALIDATION_DECIMAL->get(),
            'size' => ResponseMessage::VALIDATION_SIZE->get(),
            'gt' => ResponseMessage::VALIDATION_GT->get(),
            'after' => ResponseMessage::VALIDATION_AFTER->get(),
        ];
    }

    /**
     * Get default attribute names in Portuguese
     */
    protected function getDefaultAttributes(): array
    {
        return [
            // Common fields
            'name' => 'nome',
            'price' => 'preço',
            'quantity' => 'quantidade',
            'email' => 'e-mail',
            'password' => 'senha',
            'description' => 'descrição',
            'code' => 'código',
            'type' => 'tipo',
            'value' => 'valor',
            'status' => 'status',
            'active' => 'ativo',
            
            // Product fields
            'product_id' => 'produto',
            'sku' => 'SKU',
            'variations' => 'variações',
            
            // Customer fields
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
            
            // Address fields
            'cep' => 'CEP',
            
            // Coupon fields
            'minimum_value' => 'valor mínimo',
            'usage_limit' => 'limite de uso',
            'valid_from' => 'válido a partir de',
            'valid_until' => 'válido até',
        ];
    }
}