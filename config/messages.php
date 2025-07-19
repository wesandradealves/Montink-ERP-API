<?php

return [
    'general' => [
        'operation_success' => env('MSG_OPERATION_SUCCESS', 'Operação realizada com sucesso'),
        'resource_not_found' => env('MSG_RESOURCE_NOT_FOUND', 'Recurso não encontrado'),
        'validation_error' => env('MSG_VALIDATION_ERROR', 'Erro de validação'),
    ],
    
    'product' => [
        'created' => env('MSG_PRODUCT_CREATED', 'Produto criado com sucesso'),
        'updated' => env('MSG_PRODUCT_UPDATED', 'Produto atualizado com sucesso'),
        'deleted' => env('MSG_PRODUCT_DELETED', 'Produto excluído com sucesso'),
        'found' => env('MSG_PRODUCT_FOUND', 'Produto encontrado com sucesso'),
        'not_found' => env('MSG_PRODUCT_NOT_FOUND', 'Produto não encontrado'),
        'stock_not_found' => env('MSG_PRODUCT_STOCK_NOT_FOUND', 'Produto com identificador \'estoque\' não encontrado'),
        'variation_required' => env('MSG_PRODUCT_VARIATION_REQUIRED', 'Este produto possui variações. Especifique a variação desejada'),
    ],
    
    'order' => [
        'created' => env('MSG_ORDER_CREATED', 'Pedido criado com sucesso'),
        'found' => env('MSG_ORDER_FOUND', 'Pedido encontrado com sucesso'),
        'status_updated' => env('MSG_ORDER_STATUS_UPDATED', 'Status do pedido atualizado com sucesso'),
        'cancelled' => env('MSG_ORDER_CANCELLED', 'Pedido cancelado com sucesso'),
        'not_found' => env('MSG_ORDER_NOT_FOUND', 'Pedido não encontrado'),
        'empty_cart' => env('MSG_ORDER_EMPTY_CART', 'Carrinho vazio. Adicione produtos antes de finalizar o pedido.'),
        'cannot_cancel' => env('MSG_ORDER_CANNOT_CANCEL', 'Pedido não pode ser cancelado no status atual: :status'),
        'invalid_status' => env('MSG_ORDER_INVALID_STATUS', 'Status inválido: :status'),
    ],
    
    'cart' => [
        'item_added' => env('MSG_CART_ITEM_ADDED', 'Produto adicionado ao carrinho'),
        'item_updated' => env('MSG_CART_ITEM_UPDATED', 'Quantidade atualizada no carrinho'),
        'item_removed' => env('MSG_CART_ITEM_REMOVED', 'Produto removido do carrinho'),
        'cleared' => env('MSG_CART_CLEARED', 'Carrinho limpo com sucesso'),
        'insufficient_stock' => env('MSG_CART_INSUFFICIENT_STOCK', 'Estoque insuficiente para o produto'),
        'item_id_required' => env('MSG_CART_ITEM_ID_REQUIRED', 'ID do item é obrigatório'),
        'coupon_code_required' => env('MSG_CART_COUPON_CODE_REQUIRED', 'Código do cupom é obrigatório'),
    ],
    
    'coupon' => [
        'created' => env('MSG_COUPON_CREATED', 'Cupom criado com sucesso'),
        'updated' => env('MSG_COUPON_UPDATED', 'Cupom atualizado com sucesso'),
        'deleted' => env('MSG_COUPON_DELETED', 'Cupom excluído com sucesso'),
        'found' => env('MSG_COUPON_FOUND', 'Cupom encontrado com sucesso'),
        'not_found' => env('MSG_COUPON_NOT_FOUND', 'Cupom não encontrado'),
        'invalid' => env('MSG_COUPON_INVALID', 'Cupom inválido'),
        'expired' => env('MSG_COUPON_EXPIRED', 'Cupom expirado'),
        'minimum_not_met' => env('MSG_COUPON_MINIMUM_NOT_MET', 'Valor mínimo não atingido para usar este cupom'),
        'usage_limit_reached' => env('MSG_COUPON_USAGE_LIMIT_REACHED', 'Limite de uso do cupom atingido'),
        'already_exists' => env('MSG_COUPON_ALREADY_EXISTS', 'Cupom com este código já existe'),
        'invalid_with_reason' => env('MSG_COUPON_INVALID_WITH_REASON', 'Cupom inválido: :reason'),
    ],
    
    'address' => [
        'found' => env('MSG_ADDRESS_FOUND', 'Endereço encontrado com sucesso'),
        'not_found' => env('MSG_ADDRESS_NOT_FOUND', 'CEP não encontrado'),
        'cep_invalid' => env('MSG_ADDRESS_CEP_INVALID', 'CEP inválido'),
        'cep_invalid_format' => env('MSG_ADDRESS_CEP_INVALID_FORMAT', 'CEP deve conter 8 dígitos'),
        'cep_api_error' => env('MSG_ADDRESS_CEP_API_ERROR', 'Erro ao consultar CEP na API ViaCEP'),
    ],
    
    'stock' => [
        'insufficient' => env('MSG_STOCK_INSUFFICIENT', 'Estoque insuficiente'),
        'updated' => env('MSG_STOCK_UPDATED', 'Estoque atualizado com sucesso'),
        'insufficient_available' => env('MSG_STOCK_INSUFFICIENT_AVAILABLE', 'Estoque insuficiente. Disponível: :available'),
    ],
    
    'validation' => [
        'required' => env('MSG_VALIDATION_REQUIRED', 'O campo :attribute é obrigatório'),
        'string' => env('MSG_VALIDATION_STRING', 'O campo :attribute deve ser um texto'),
        'numeric' => env('MSG_VALIDATION_NUMERIC', 'O campo :attribute deve ser um número'),
        'integer' => env('MSG_VALIDATION_INTEGER', 'O campo :attribute deve ser um número inteiro'),
        'email' => env('MSG_VALIDATION_EMAIL', 'O campo :attribute deve ser um email válido'),
        'min' => env('MSG_VALIDATION_MIN', 'O campo :attribute deve ser no mínimo :min'),
        'max' => env('MSG_VALIDATION_MAX', 'O campo :attribute não pode ser maior que :max'),
        'unique' => env('MSG_VALIDATION_UNIQUE', 'Este :attribute já está em uso'),
        'exists' => env('MSG_VALIDATION_EXISTS', ':Attribute não encontrado'),
        'boolean' => env('MSG_VALIDATION_BOOLEAN', 'O campo :attribute deve ser verdadeiro ou falso'),
        'array' => env('MSG_VALIDATION_ARRAY', 'O campo :attribute deve ser uma lista'),
        'date' => env('MSG_VALIDATION_DATE', 'O campo :attribute deve ser uma data válida'),
        'date_format' => env('MSG_VALIDATION_DATE_FORMAT', 'O campo :attribute deve estar no formato :format'),
        'in' => env('MSG_VALIDATION_IN', 'O campo :attribute selecionado é inválido'),
        'decimal' => env('MSG_VALIDATION_DECIMAL', 'O campo :attribute deve ter :decimal casas decimais'),
        'size' => env('MSG_VALIDATION_SIZE', 'O campo :attribute deve ter :size caracteres'),
        'gt' => env('MSG_VALIDATION_GT', 'O campo :attribute deve ser maior que :value'),
        'after' => env('MSG_VALIDATION_AFTER', 'O campo :attribute deve ser uma data posterior a :date'),
    ],
];