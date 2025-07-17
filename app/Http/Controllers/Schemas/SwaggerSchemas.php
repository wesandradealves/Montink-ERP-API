<?php

namespace App\Http\Controllers\Schemas;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Modelo de Produto",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Notebook Dell"),
 *     @OA\Property(property="description", type="string", example="Notebook Dell Inspiron 15"),
 *     @OA\Property(property="price", type="number", format="float", example=2999.90),
 *     @OA\Property(property="sku", type="string", example="NTB-DELL-001"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="variations", type="object", example={"cor": "preto", "tamanho": "15 polegadas"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     title="API Response",
 *     description="Formato padrão de resposta da API",
 *     @OA\Property(property="data", type="object", description="Dados da resposta"),
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiListResponse",
 *     type="object",
 *     title="API List Response",
 *     description="Formato padrão de resposta de listagem",
 *     @OA\Property(property="data", type="array", @OA\Items(type="object"), description="Lista de itens"),
 *     @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer", example=10))
 * )
 * 
 * @OA\Schema(
 *     schema="ApiErrorResponse",
 *     type="object",
 *     title="API Error Response",
 *     description="Formato padrão de resposta de erro",
 *     @OA\Property(property="error", type="string", example="Erro ao processar solicitação")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error",
 *     description="Resposta de erro de validação",
 *     @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Erros de validação por campo",
 *         @OA\Property(property="name", type="array", @OA\Items(type="string", example="O campo nome é obrigatório")),
 *         @OA\Property(property="price", type="array", @OA\Items(type="string", example="O campo preço deve ser numérico"))
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Modelo de Pedido",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="customer_name", type="string", example="João Silva"),
 *     @OA\Property(property="customer_email", type="string", format="email", example="joao@email.com"),
 *     @OA\Property(property="customer_phone", type="string", example="(11) 98765-4321"),
 *     @OA\Property(property="customer_cpf", type="string", example="123.456.789-00"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=100.00),
 *     @OA\Property(property="discount", type="number", format="float", example=10.00),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=20.00),
 *     @OA\Property(property="total", type="number", format="float", example=110.00),
 *     @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="pending"),
 *     @OA\Property(property="coupon_code", type="string", example="DESC10"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Coupon",
 *     type="object",
 *     title="Coupon",
 *     description="Modelo de Cupom",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="DESC10"),
 *     @OA\Property(property="description", type="string", example="10% de desconto"),
 *     @OA\Property(property="type", type="string", enum={"fixed", "percentage"}, example="percentage"),
 *     @OA\Property(property="value", type="number", format="float", example=10.00),
 *     @OA\Property(property="minimum_value", type="number", format="float", example=50.00),
 *     @OA\Property(property="usage_limit", type="integer", example=100),
 *     @OA\Property(property="used_count", type="integer", example=10),
 *     @OA\Property(property="valid_from", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="valid_until", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Stock",
 *     type="object",
 *     title="Stock",
 *     description="Modelo de Estoque",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=100),
 *     @OA\Property(property="reserved", type="integer", example=10),
 *     @OA\Property(property="available", type="integer", example=90, description="Calculado automaticamente (quantity - reserved)"),
 *     @OA\Property(property="variation", type="object", example={"cor": "preto", "tamanho": "M"}),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="HealthCheck",
 *     type="object",
 *     title="Health Check",
 *     description="Resposta de verificação de saúde",
 *     @OA\Property(property="status", type="string", example="healthy"),
 *     @OA\Property(property="timestamp", type="string", format="date-time", example="2024-01-01T00:00:00-03:00"),
 *     @OA\Property(property="version", type="string", example="0.2.0")
 * )
 */
class SwaggerSchemas
{
    // Esta classe existe apenas para definir schemas do Swagger
}