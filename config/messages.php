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
    ],
    
    'order' => [
        'created' => env('MSG_ORDER_CREATED', 'Pedido criado com sucesso'),
        'found' => env('MSG_ORDER_FOUND', 'Pedido encontrado com sucesso'),
        'status_updated' => env('MSG_ORDER_STATUS_UPDATED', 'Status do pedido atualizado com sucesso'),
        'cancelled' => env('MSG_ORDER_CANCELLED', 'Pedido cancelado com sucesso'),
        'not_found' => env('MSG_ORDER_NOT_FOUND', 'Pedido não encontrado'),
        'empty_cart' => env('MSG_ORDER_EMPTY_CART', 'Carrinho vazio. Adicione produtos antes de finalizar o pedido.'),
    ],
    
    'cart' => [
        'item_added' => env('MSG_CART_ITEM_ADDED', 'Produto adicionado ao carrinho'),
        'item_updated' => env('MSG_CART_ITEM_UPDATED', 'Quantidade atualizada no carrinho'),
        'item_removed' => env('MSG_CART_ITEM_REMOVED', 'Produto removido do carrinho'),
        'cleared' => env('MSG_CART_CLEARED', 'Carrinho limpo com sucesso'),
        'insufficient_stock' => env('MSG_CART_INSUFFICIENT_STOCK', 'Estoque insuficiente para o produto'),
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
    ],
    
    'address' => [
        'found' => env('MSG_ADDRESS_FOUND', 'Endereço encontrado com sucesso'),
        'not_found' => env('MSG_ADDRESS_NOT_FOUND', 'CEP não encontrado'),
        'cep_invalid' => env('MSG_ADDRESS_CEP_INVALID', 'CEP inválido'),
    ],
    
    'stock' => [
        'insufficient' => env('MSG_STOCK_INSUFFICIENT', 'Estoque insuficiente'),
        'updated' => env('MSG_STOCK_UPDATED', 'Estoque atualizado com sucesso'),
    ],
];