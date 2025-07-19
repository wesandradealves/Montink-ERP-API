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
            $newStatusEnum = OrderStatus::from($status);
            
            if ($newStatusEnum === OrderStatus::CANCELLED) {
                $order->delete();
                
                Log::info(ResponseMessage::LOG_ORDER_REMOVED_WEBHOOK->get(), [
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

            $order->status = $newStatusEnum->value;
            $order->save();

            Log::info(ResponseMessage::LOG_ORDER_STATUS_UPDATED_WEBHOOK->get(), [
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatusEnum->value,
                'timestamp' => $timestamp,
                'notes' => $notes
            ]);

            return [
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatusEnum->value,
                'action' => 'updated',
                'updated_at' => now()->toISOString()
            ];
        });
    }
}