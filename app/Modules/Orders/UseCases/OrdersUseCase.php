<?php

namespace App\Modules\Orders\UseCases;

use App\Common\Services\SessionService;
use App\Modules\Orders\DTOs\CreateOrderDTO;
use App\Modules\Orders\DTOs\OrderDTO;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\ShippingService;
use App\Modules\Stock\Models\Stock;
use Illuminate\Support\Facades\DB;

class OrdersUseCase
{
    public function __construct(
        private ShippingService $shippingService
    ) {}

    public function createOrder(CreateOrderDTO $dto): OrderDTO
    {
        return DB::transaction(function () use ($dto) {
            $sessionId = SessionService::getCurrentId();
            
            $cartItems = CartItem::with('product')
                ->where('session_id', $sessionId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception("Carrinho vazio. Adicione produtos antes de finalizar o pedido.");
            }

            $subtotal = $cartItems->sum(fn($item) => $item->getSubtotal());
            $shippingCost = $this->shippingService->calculateShipping($subtotal);
            $discount = 0;

            if ($dto->couponCode) {
                $discount = $this->calculateCouponDiscount($dto->couponCode, $subtotal);
            }

            $total = $subtotal - $discount + $shippingCost;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $dto->customerName,
                'customer_email' => $dto->customerEmail,
                'customer_phone' => $dto->customerPhone,
                'customer_cpf' => $dto->customerCpf,
                'customer_cep' => $dto->customerCep,
                'customer_address' => $dto->customerAddress,
                'customer_complement' => $dto->customerComplement,
                'customer_neighborhood' => $dto->customerNeighborhood,
                'customer_city' => $dto->customerCity,
                'customer_state' => $dto->customerState,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'coupon_code' => $dto->couponCode,
                'session_id' => $sessionId,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->getSubtotal(),
                    'variations' => $cartItem->variations,
                ]);

                $this->updateStockReservation($cartItem->product_id, $cartItem->quantity, $cartItem->variations);
            }

            CartItem::where('session_id', $sessionId)->delete();

            $order->load('items');

            return OrderDTO::fromModel($order);
        });
    }

    public function getOrder(int $orderId): OrderDTO
    {
        $order = Order::with('items')->findOrFail($orderId);
        return OrderDTO::fromModel($order);
    }

    public function getOrderByNumber(string $orderNumber): OrderDTO
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();
        
        return OrderDTO::fromModel($order);
    }

    public function listOrders(array $filters = []): array
    {
        $query = Order::with('items');

        if (isset($filters['customer_email'])) {
            $query->byCustomerEmail($filters['customer_email']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return [
            'items' => $orders->map(fn($order) => OrderDTO::fromModel($order))->toArray(),
            'total' => $orders->count(),
        ];
    }

    public function updateOrderStatus(int $orderId, string $status): OrderDTO
    {
        $order = Order::findOrFail($orderId);
        
        if ($status === 'cancelled' && $order->canBeCancelled()) {
            $this->releaseStockReservations($order);
        }

        $order->updateStatus($status);
        $order->load('items');

        return OrderDTO::fromModel($order);
    }

    public function cancelOrder(int $orderId): OrderDTO
    {
        $order = Order::with('items')->findOrFail($orderId);
        
        $this->releaseStockReservations($order);
        $order->cancel();

        return OrderDTO::fromModel($order);
    }

    private function calculateCouponDiscount(string $couponCode, float $subtotal): float
    {
        return 0;
    }

    private function updateStockReservation(int $productId, int $quantity, ?array $variations = null): void
    {
        $stock = Stock::where('product_id', $productId)
            ->when($variations, function ($query) use ($variations) {
                return $query->where('variations', json_encode($variations));
            })
            ->first();

        if ($stock) {
            $stock->reserved += $quantity;
            $stock->save();
        }
    }

    private function releaseStockReservations(Order $order): void
    {
        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->when($item->variations, function ($query) use ($item) {
                    return $query->where('variations', json_encode($item->variations));
                })
                ->first();

            if ($stock) {
                $stock->reserved = max(0, $stock->reserved - $item->quantity);
                $stock->save();
            }
        }
    }
}