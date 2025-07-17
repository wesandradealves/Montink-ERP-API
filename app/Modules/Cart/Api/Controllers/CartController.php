<?php

namespace App\Modules\Cart\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Api\Requests\AddToCartRequest;
use App\Modules\Cart\Api\Requests\UpdateCartItemRequest;
use App\Modules\Cart\DTOs\AddToCartDTO;
use App\Modules\Cart\UseCases\CartUseCase;
use App\Common\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    use ApiResponseTrait;

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
        try {
            $cart = $this->cartUseCase->getCart();
            
            return $this->successResponse(
                data: $cart,
                message: 'Carrinho obtido com sucesso'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Erro ao obter carrinho: ' . $e->getMessage()
            );
        }
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
        try {
            $dto = new AddToCartDTO(
                productId: $request->input('product_id'),
                quantity: $request->input('quantity'),
                variations: $request->input('variations'),
            );

            $cart = $this->cartUseCase->addToCart($dto);

            return $this->successResponse(
                data: $cart,
                message: 'Produto adicionado ao carrinho com sucesso',
                statusCode: 201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Erro ao adicionar produto: ' . $e->getMessage(),
                statusCode: 400
            );
        }
    }

    /**
     * @OA\Put(
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
        try {
            $cart = $this->cartUseCase->updateQuantity($id, $request->input('quantity'));

            return $this->successResponse(
                data: $cart,
                message: 'Quantidade atualizada com sucesso'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Erro ao atualizar quantidade: ' . $e->getMessage(),
                statusCode: 400
            );
        }
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
        try {
            $cart = $this->cartUseCase->removeFromCart($id);

            return $this->successResponse(
                data: $cart,
                message: 'Item removido do carrinho com sucesso'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Erro ao remover item: ' . $e->getMessage(),
                statusCode: 404
            );
        }
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
        try {
            $this->cartUseCase->clearCart();

            return $this->successResponse(
                message: 'Carrinho limpo com sucesso'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Erro ao limpar carrinho: ' . $e->getMessage()
            );
        }
    }
}