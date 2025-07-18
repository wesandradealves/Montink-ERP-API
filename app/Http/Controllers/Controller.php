<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Montink ERP API",
 *     description="API para sistema Mini ERP Montink com gestão de produtos, pedidos, cupons e estoque",
 *     @OA\Contact(
 *         email="admin@montink.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Endpoints para gestão de produtos"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Endpoints para gestão do carrinho de compras"
 * )
 * 
 * @OA\Tag(
 *     name="Address",
 *     description="Endpoints para validação de endereços e CEP"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Endpoints para gestão de pedidos"
 * )
 * 
 * @OA\Tag(
 *     name="Coupons",
 *     description="Endpoints para gestão de cupons"
 * )
 * 
 * @OA\Tag(
 *     name="Stock",
 *     description="Endpoints para gestão de estoque"
 * )
 * 
 * @OA\Tag(
 *     name="Health",
 *     description="Endpoints de verificação de saúde da API"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}