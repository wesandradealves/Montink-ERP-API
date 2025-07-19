<?php

namespace App\Modules\Webhooks\UseCases;

use App\Common\Enums\ResponseMessage;
use App\Domain\Commons\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookUseCase
{
    public function processOrderStatus(
        int $orderId,
        string $status,
        ?string $timestamp = null,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use ($orderId, $status, $timestamp, $notes) {
            $order = Order::find($orderId);
            
            if (!$order) {
                throw new \Exception(ResponseMessage::ORDER_NOT_FOUND->get(), 404);
            }

            $oldStatus = $order->status;
            $newStatus = OrderStatus::from($status);

            if ($newStatus === OrderStatus::CANCELLED) {
                $order->delete();
                
                Log::info('Pedido removido via webhook', [
                    'order_id' => $orderId,
                    'action' => 'deleted',
                    'timestamp' => $timestamp,
                    'notes' => $notes
                ]);

                return [
                    'order_id' => $orderId,
                    'status' => $status,
                    'action' => 'deleted',
                    'message' => ResponseMessage::ORDER_CANCELLED->get()
                ];
            }

            $order->status = $newStatus;
            $order->save();

            Log::info('Status do pedido atualizado via webhook', [
                'order_id' => $orderId,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'timestamp' => $timestamp,
                'notes' => $notes
            ]);

            return [
                'order_id' => $orderId,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'action' => 'updated',
                'updated_at' => now()->toISOString()
            ];
        });
    }
}