<?php

namespace App\Modules\Cart\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use App\Common\Services\SessionService;
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
    
    private function withSessionCookie(JsonResponse $response): JsonResponse
    {
        $sessionId = SessionService::getCurrentId();
        $cookie = new \Symfony\Component\HttpFoundation\Cookie('session_id', $sessionId, time() + (60 * 60 * 24));
        return $response->withCookie($cookie);
    }

    /**
     * @OA\Get(
     *     path="/api/cart",
     *     operationId="getCart",
     *     summary="Obter carrinho atual",
     *     description="Retorna todos os itens do carrinho da sessão atual com cálculo de frete",
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
        $response = $this->handleUseCaseExecution(function() {
            return $this->cartUseCase->getCart();
        });
        
        return $this->withSessionCookie($response);
    }

    /**
     * @OA\Post(
     *     path="/api/cart",
     *     operationId="addToCart",
     *     summary="Adicionar produto ao carrinho",
     *     description="Adiciona um produto ao carrinho com validação de estoque",
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
        $response = $this->handleUseCaseCreation(function() use ($request) {
            $dto = new AddToCartDTO(
                productId: $request->input('product_id'),
                quantity: $request->input('quantity'),
                variations: $request->input('variations'),
            );

            return $this->cartUseCase->addToCart($dto);
        }, ResponseMessage::PRODUCT_ADDED_TO_CART->get());
        
        return $this->withSessionCookie($response);
    }

    /**
     * @OA\Patch(
     *     path="/api/cart/{id}",
     *     operationId="updateCartItem",
     *     summary="Atualizar quantidade de item do carrinho",
     *     description="Atualiza a quantidade de um item específico no carrinho com validação de estoque",
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
    public function update(UpdateCartItemRequest $request, $id = null): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request, $id) {
            $itemId = $id ? (int)$id : $request->input('id');
            if (!$itemId) {
                throw new \InvalidArgumentException(ResponseMessage::CART_ITEM_ID_REQUIRED->get());
            }
            return $this->cartUseCase->updateQuantity($itemId, $request->input('quantity'));
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/{id}",
     *     operationId="removeFromCart",
     *     summary="Remover item do carrinho",
     *     description="Remove um item específico do carrinho",
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
     *     operationId="clearCart",
     *     summary="Limpar carrinho",
     *     description="Remove todos os itens do carrinho da sessão atual",
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

    /**
     * @OA\Post(
     *     path="/api/cart/coupon",
     *     operationId="applyCouponToCart",
     *     summary="Aplicar cupom ao carrinho",
     *     description="Aplica um cupom de desconto ao carrinho atual",
     *     tags={"Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="DESCONTO10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cupom aplicado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Cupom inválido ou expirado"
     *     )
     * )
     */
    public function applyCoupon(\Illuminate\Http\Request $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $code = $request->input('code');
            if (!$code) {
                throw new \InvalidArgumentException(ResponseMessage::CART_COUPON_CODE_REQUIRED->get());
            }
            
            // Por enquanto, apenas retorna sucesso para teste
            return [
                'success' => true,
                'message' => 'Cupom aplicado com sucesso',
                'discount' => 10
            ];
        });
    }
}