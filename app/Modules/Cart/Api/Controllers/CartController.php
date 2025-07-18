<?php

namespace App\Modules\Cart\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Modules\Cart\Api\Requests\AddToCartRequest;
use App\Modules\Cart\Api\Requests\UpdateCartItemRequest;
use App\Modules\Cart\DTOs\AddToCartDTO;
use App\Modules\Cart\UseCases\CartUseCase;
use Illuminate\Http\JsonResponse;

class CartController extends BaseApiController
{

    public function __construct(
        private CartUseCase $cartUseCase
    ) {}

    /**
     * @OA\Get(
     *     path="/api/cart",
     *     summary="Obter carrinho atual",
     *     tags={"Cart"},
     *     @OA\Response(
     *         response=200,
     *         description="Carrinho retornado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/CartItem")),
     *                 @OA\Property(property="subtotal", type="number", example=199.98),
     *                 @OA\Property(property="total_items", type="integer", example=2)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->handleUseCaseExecution(function() {
            return $this->cartUseCase->getCart();
        });
    }

    /**
     * @OA\Post(
     *     path="/api/cart",
     *     summary="Adicionar produto ao carrinho",
     *     tags={"Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="variations", type="object", example={"size": "M", "color": "blue"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto adicionado ao carrinho",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação ou estoque insuficiente"
     *     )
     * )
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $dto = new AddToCartDTO(
                productId: $request->input('product_id'),
                quantity: $request->input('quantity'),
                variations: $request->input('variations'),
            );

            return $this->cartUseCase->addToCart($dto);
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/cart/{id}",
     *     summary="Atualizar quantidade de item do carrinho",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantidade atualizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação ou estoque insuficiente"
     *     )
     * )
     */
    public function update(UpdateCartItemRequest $request, int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request, $id) {
            return $this->cartUseCase->updateQuantity($id, $request->input('quantity'));
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/{id}",
     *     summary="Remover item do carrinho",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item não encontrado"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($id) {
            return $this->cartUseCase->removeFromCart($id);
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/cart",
     *     summary="Limpar carrinho",
     *     tags={"Cart"},
     *     @OA\Response(
     *         response=200,
     *         description="Carrinho limpo com sucesso"
     *     )
     * )
     */
    public function clear(): JsonResponse
    {
        return $this->handleUseCaseExecution(function() {
            $this->cartUseCase->clearCart();
            return null;
        });
    }
}