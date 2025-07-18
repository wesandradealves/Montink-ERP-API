<?php

namespace App\Common\OpenApi;

/**
 * @OA\Parameter(
 *     parameter="id",
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="ID do recurso",
 *     @OA\Schema(type="integer", example=1)
 * )
 * 
 * @OA\Parameter(
 *     parameter="page",
 *     name="page",
 *     in="query",
 *     required=false,
 *     description="Número da página",
 *     @OA\Schema(type="integer", example=1)
 * )
 * 
 * @OA\Parameter(
 *     parameter="per_page",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     description="Itens por página",
 *     @OA\Schema(type="integer", example=10)
 * )
 * 
 * @OA\Parameter(
 *     parameter="search",
 *     name="search",
 *     in="query",
 *     required=false,
 *     description="Termo de busca",
 *     @OA\Schema(type="string", example="exemplo")
 * )
 * 
 * @OA\Parameter(
 *     parameter="active",
 *     name="active",
 *     in="query",
 *     required=false,
 *     description="Filtrar por status ativo",
 *     @OA\Schema(type="boolean", example=true)
 * )
 * 
 * @OA\Parameter(
 *     parameter="sort",
 *     name="sort",
 *     in="query",
 *     required=false,
 *     description="Campo para ordenação",
 *     @OA\Schema(type="string", example="created_at")
 * )
 * 
 * @OA\Parameter(
 *     parameter="direction",
 *     name="direction",
 *     in="query",
 *     required=false,
 *     description="Direção da ordenação",
 *     @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
 * )
 */
class Parameters
{
    // Esta classe serve apenas para documentação Swagger
}