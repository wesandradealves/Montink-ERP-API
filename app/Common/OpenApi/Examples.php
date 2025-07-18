<?php

namespace App\Common\OpenApi;

/**
 * @OA\Examples(
 *     example="product_create",
 *     summary="Criar produto",
 *     value={
 *         "name": "Produto Exemplo",
 *         "sku": "PROD-001",
 *         "price": 99.99,
 *         "description": "Descrição do produto",
 *         "active": true
 *     }
 * )
 * 
 * @OA\Examples(
 *     example="coupon_create",
 *     summary="Criar cupom",
 *     value={
 *         "code": "DESCONTO10",
 *         "description": "Cupom de 10% de desconto",
 *         "type": "percentage",
 *         "value": 10,
 *         "minimum_value": 50,
 *         "usage_limit": 100,
 *         "valid_from": "2025-07-01",
 *         "valid_until": "2025-12-31",
 *         "active": true
 *     }
 * )
 * 
 * @OA\Examples(
 *     example="order_create",
 *     summary="Criar pedido",
 *     value={
 *         "customer_name": "João Silva",
 *         "customer_email": "joao@example.com",
 *         "customer_phone": "(11) 98765-4321",
 *         "customer_cpf": "123.456.789-00",
 *         "customer_cep": "01310-100",
 *         "customer_address": "Av. Paulista, 1000",
 *         "customer_complement": "Apto 101",
 *         "customer_neighborhood": "Bela Vista",
 *         "customer_city": "São Paulo",
 *         "customer_state": "SP",
 *         "coupon_code": "DESCONTO10"
 *     }
 * )
 * 
 * @OA\Examples(
 *     example="cart_add",
 *     summary="Adicionar ao carrinho",
 *     value={
 *         "product_id": 1,
 *         "quantity": 2,
 *         "variations": {
 *             "color": "Azul",
 *             "size": "M"
 *         }
 *     }
 * )
 */
class Examples
{
    // Esta classe serve apenas para documentação Swagger
}