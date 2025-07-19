<?php

namespace App\Modules\Webhooks\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use App\Modules\Webhooks\Api\Requests\OrderStatusWebhookRequest;
use App\Modules\Webhooks\UseCases\WebhookUseCase;
use Illuminate\Http\JsonResponse;

class WebhookController extends BaseApiController
{
    public function __construct(
        private WebhookUseCase $webhookUseCase
    ) {}

    /**
     * @OA\Post(
     *     path="/api/webhooks/order-status",
     *     operationId="webhookOrderStatus",
     *     summary="Webhook para atualização de status de pedido",
     *     description="Recebe notificações externas para atualizar status de pedidos. Se o status for 'cancelado', o pedido é removido do sistema.",
     *     tags={"Webhooks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "status"},
     *             @OA\Property(property="order_id", type="integer", example=123, description="ID do pedido"),
     *             @OA\Property(
     *                 property="status", 
     *                 type="string", 
     *                 enum={"pending", "processing", "shipped", "delivered", "cancelled"},
     *                 example="processing",
     *                 description="Novo status do pedido"
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-01-18T10:30:00Z", description="Data/hora da mudança (opcional)"),
     *             @OA\Property(property="notes", type="string", example="Pedido enviado via transportadora X", description="Observações adicionais (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Webhook processado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Status do pedido atualizado com sucesso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="order_id", type="integer", example=123),
     *                 @OA\Property(property="status", type="string", example="processing"),
     *                 @OA\Property(property="action", type="string", example="updated", description="Ação realizada: updated ou deleted")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Pedido não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function orderStatus(OrderStatusWebhookRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function() use ($request) {
            $result = $this->webhookUseCase->processOrderStatus(
                orderId: $request->input('order_id'),
                status: $request->input('status'),
                timestamp: $request->input('timestamp'),
                notes: $request->input('notes')
            );
            
            return $result;
        });
    }
}