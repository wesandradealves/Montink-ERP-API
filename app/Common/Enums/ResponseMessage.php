<?php

namespace App\Common\Enums;

enum ResponseMessage: string
{
    // Mensagens gerais
    case OPERATION_SUCCESS = 'messages.general.operation_success';
    case RESOURCE_NOT_FOUND = 'messages.general.resource_not_found';
    case VALIDATION_ERROR = 'messages.general.validation_error';
    
    // Mensagens de produtos
    case PRODUCT_CREATED = 'messages.product.created';
    case PRODUCT_UPDATED = 'messages.product.updated';
    case PRODUCT_DELETED = 'messages.product.deleted';
    case PRODUCT_FOUND = 'messages.product.found';
    case PRODUCT_NOT_FOUND = 'messages.product.not_found';
    
    // Mensagens de pedidos
    case ORDER_CREATED = 'messages.order.created';
    case ORDER_FOUND = 'messages.order.found';
    case ORDER_STATUS_UPDATED = 'messages.order.status_updated';
    case ORDER_CANCELLED = 'messages.order.cancelled';
    case ORDER_NOT_FOUND = 'messages.order.not_found';
    case ORDER_EMPTY_CART = 'messages.order.empty_cart';
    
    // Mensagens de carrinho
    case CART_ITEM_ADDED = 'messages.cart.item_added';
    case CART_ITEM_UPDATED = 'messages.cart.item_updated';
    case CART_ITEM_REMOVED = 'messages.cart.item_removed';
    case CART_CLEARED = 'messages.cart.cleared';
    case CART_INSUFFICIENT_STOCK = 'messages.cart.insufficient_stock';
    
    // Mensagens de cupons
    case COUPON_CREATED = 'messages.coupon.created';
    case COUPON_UPDATED = 'messages.coupon.updated';
    case COUPON_DELETED = 'messages.coupon.deleted';
    case COUPON_FOUND = 'messages.coupon.found';
    case COUPON_NOT_FOUND = 'messages.coupon.not_found';
    case COUPON_INVALID = 'messages.coupon.invalid';
    case COUPON_EXPIRED = 'messages.coupon.expired';
    case COUPON_MINIMUM_NOT_MET = 'messages.coupon.minimum_not_met';
    case COUPON_USAGE_LIMIT_REACHED = 'messages.coupon.usage_limit_reached';
    case COUPON_ALREADY_EXISTS = 'messages.coupon.already_exists';
    
    // Mensagens de endereço
    case ADDRESS_FOUND = 'messages.address.found';
    case ADDRESS_NOT_FOUND = 'messages.address.not_found';
    case ADDRESS_CEP_INVALID = 'messages.address.cep_invalid';
    
    // Mensagens de estoque
    case STOCK_INSUFFICIENT = 'messages.stock.insufficient';
    case STOCK_UPDATED = 'messages.stock.updated';
    
    public function get(): string
    {
        return config($this->value) ?? $this->getDefault();
    }
    
    private function getDefault(): string
    {
        return match($this) {
            // Mensagens gerais
            self::OPERATION_SUCCESS => 'Operação realizada com sucesso',
            self::RESOURCE_NOT_FOUND => 'Recurso não encontrado',
            self::VALIDATION_ERROR => 'Erro de validação',
            
            // Mensagens de produtos
            self::PRODUCT_CREATED => 'Produto criado com sucesso',
            self::PRODUCT_UPDATED => 'Produto atualizado com sucesso',
            self::PRODUCT_DELETED => 'Produto excluído com sucesso',
            self::PRODUCT_FOUND => 'Produto encontrado com sucesso',
            self::PRODUCT_NOT_FOUND => 'Produto não encontrado',
            
            // Mensagens de pedidos
            self::ORDER_CREATED => 'Pedido criado com sucesso',
            self::ORDER_FOUND => 'Pedido encontrado com sucesso',
            self::ORDER_STATUS_UPDATED => 'Status do pedido atualizado com sucesso',
            self::ORDER_CANCELLED => 'Pedido cancelado com sucesso',
            self::ORDER_NOT_FOUND => 'Pedido não encontrado',
            self::ORDER_EMPTY_CART => 'Carrinho vazio. Adicione produtos antes de finalizar o pedido.',
            
            // Mensagens de carrinho
            self::CART_ITEM_ADDED => 'Produto adicionado ao carrinho',
            self::CART_ITEM_UPDATED => 'Quantidade atualizada no carrinho',
            self::CART_ITEM_REMOVED => 'Produto removido do carrinho',
            self::CART_CLEARED => 'Carrinho limpo com sucesso',
            self::CART_INSUFFICIENT_STOCK => 'Estoque insuficiente para o produto',
            
            // Mensagens de cupons
            self::COUPON_CREATED => 'Cupom criado com sucesso',
            self::COUPON_UPDATED => 'Cupom atualizado com sucesso',
            self::COUPON_DELETED => 'Cupom excluído com sucesso',
            self::COUPON_FOUND => 'Cupom encontrado com sucesso',
            self::COUPON_NOT_FOUND => 'Cupom não encontrado',
            self::COUPON_INVALID => 'Cupom inválido',
            self::COUPON_EXPIRED => 'Cupom expirado',
            self::COUPON_MINIMUM_NOT_MET => 'Valor mínimo não atingido para usar este cupom',
            self::COUPON_USAGE_LIMIT_REACHED => 'Limite de uso do cupom atingido',
            self::COUPON_ALREADY_EXISTS => 'Cupom com este código já existe',
            
            // Mensagens de endereço
            self::ADDRESS_FOUND => 'Endereço encontrado com sucesso',
            self::ADDRESS_NOT_FOUND => 'CEP não encontrado',
            self::ADDRESS_CEP_INVALID => 'CEP inválido',
            
            // Mensagens de estoque
            self::STOCK_INSUFFICIENT => 'Estoque insuficiente',
            self::STOCK_UPDATED => 'Estoque atualizado com sucesso',
        };
    }
}