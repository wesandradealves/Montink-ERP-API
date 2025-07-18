<?php

namespace App\Modules\Orders\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use App\Modules\Orders\Api\Requests\CreateOrderRequest;
use App\Modules\Orders\Api\Requests\UpdateOrderStatusRequest;
use App\Modules\Orders\DTOs\CreateOrderDTO;
use App\Modules\Orders\UseCases\OrdersUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{

    public function __construct(
        private OrdersUseCase $ordersUseCase
    ) {}

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Criar novo pedido",
     *     description="Finaliza o carrinho e cria um novo pedido. Um email de confirmação será enviado automaticamente para o endereço informado.",
     *     operationId="createOrder",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_name","customer_email","customer_cep","customer_address","customer_neighborhood","customer_city","customer_state"},
     *             @OA\Property(property="customer_name", type="string", example="João Silva"),
     *             @OA\Property(property="customer_email", type="string", format="email", example="joao@email.com"),
     *             @OA\Property(property="customer_phone", type="string", example="(11) 98765-4321"),
     *             @OA\Property(property="customer_cpf", type="string", example="123.456.789-00"),
     *             @OA\Property(property="customer_cep", type="string", example="01310-100"),
     *             @OA\Property(property="customer_address", type="string", example="Avenida Paulista, 1000"),
     *             @OA\Property(property="customer_complement", type="string", example="Apto 101"),
     *             @OA\Property(property="customer_neighborhood", type="string", example="Bela Vista"),
     *             @OA\Property(property="customer_city", type="string", example="São Paulo"),
     *             @OA\Property(property="customer_state", type="string", maxLength=2, example="SP"),
     *             @OA\Property(property="coupon_code", type="string", example="DESC10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido criado com sucesso e email de confirmação enviado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string", example="Pedido criado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Carrinho vazio ou dados inválidos"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        return $this->handleUseCaseCreation(function() use ($request) {
            $dto = CreateOrderDTO::fromArray($request->validated());
            return $this->ordersUseCase->createOrder($dto);
        }, ResponseMessage::ORDER_CREATED->get());
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Listar pedidos",
     *     description="Lista pedidos com filtros opcionais",
     *     operationId="listOrders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="customer_email",
     *         in="query",
     *         description="Filtrar por email do cliente",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending","processing","shipped","delivered","cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Data inicial (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Data final (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $filters = $request->only(['customer_email', 'status', 'date_from', 'date_to']);
            return $this->ordersUseCase->listOrders($filters);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Buscar pedido por ID",
     *     description="Retorna detalhes de um pedido específico",
     *     operationId="getOrder",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do pedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string", example="Pedido encontrado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($id) {
            $order = $this->ordersUseCase->getOrder($id);
            return $this->successResponse($order, ResponseMessage::ORDER_FOUND->get());
        });
    }

    /**
     * @OA\Get(
     *     path="/api/orders/number/{orderNumber}",
     *     summary="Buscar pedido por número",
     *     description="Retorna detalhes de um pedido pelo número",
     *     operationId="getOrderByNumber",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="orderNumber",
     *         in="path",
     *         description="Número do pedido",
     *         required=true,
     *         @OA\Schema(type="string", example="ORD-20250117-0001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do pedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string", example="Pedido encontrado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function showByNumber(string $orderNumber): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($orderNumber) {
            $order = $this->ordersUseCase->getOrderByNumber($orderNumber);
            return $this->successResponse($order, ResponseMessage::ORDER_FOUND->get());
        });
    }

    /**
     * @OA\Patch(
     *     path="/api/orders/{id}/status",
     *     summary="Atualizar status do pedido",
     *     description="Atualiza o status de um pedido",
     *     operationId="updateOrderStatus",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status", 
     *                 type="string", 
     *                 enum={"pending","processing","shipped","delivered","cancelled"},
     *                 example="processing"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string", example="Status do pedido atualizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Status inválido ou pedido não pode ser atualizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request, $id) {
            $order = $this->ordersUseCase->updateOrderStatus($id, $request->status);
            return $this->successResponse($order, 'Status do pedido atualizado com sucesso');
        });
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Cancelar pedido",
     *     description="Cancela um pedido se possível",
     *     operationId="cancelOrder",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido cancelado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string", example="Pedido cancelado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Pedido não pode ser cancelado no status atual"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($id) {
            $order = $this->ordersUseCase->cancelOrder($id);
            return $this->successResponse($order, 'Pedido cancelado com sucesso');
        });
    }
}