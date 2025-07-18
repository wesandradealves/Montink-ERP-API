<?php

namespace App\Common\OpenApi;

/**
 * @OA\Schema(
 *     schema="NotFoundResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Recurso não encontrado"),
 *     @OA\Property(property="errors", type="object")
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Operação realizada com sucesso")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="total", type="integer", example=50),
 *     @OA\Property(property="from", type="integer", example=1),
 *     @OA\Property(property="to", type="integer", example=10)
 * )
 * 
 * @OA\Schema(
 *     schema="MoneyValue",
 *     type="number",
 *     format="float",
 *     example=99.99,
 *     description="Valor monetário com até 2 casas decimais"
 * )
 * 
 * @OA\Schema(
 *     schema="CepFormat",
 *     type="string",
 *     pattern="^\d{5}-\d{3}$",
 *     example="01310-100",
 *     description="CEP no formato 00000-000"
 * )
 * 
 * @OA\Schema(
 *     schema="CpfFormat",
 *     type="string",
 *     pattern="^\d{3}\.\d{3}\.\d{3}-\d{2}$",
 *     example="123.456.789-00",
 *     description="CPF no formato 000.000.000-00"
 * )
 * 
 * @OA\Schema(
 *     schema="Timestamp",
 *     type="string",
 *     format="date-time",
 *     example="2025-07-17 12:00:00",
 *     description="Data e hora no formato Y-m-d H:i:s"
 * )
 */
class Schemas
{
    // Esta classe serve apenas para documentação Swagger
}