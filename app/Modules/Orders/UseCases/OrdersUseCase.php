<?php

namespace App\Modules\Orders\UseCases;

use App\Common\Enums\ResponseMessage;
use App\Common\Services\SessionService;
use App\Modules\Orders\DTOs\CreateOrderDTO;
use App\Modules\Orders\DTOs\OrderDTO;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Services\ShippingService;
use App\Modules\Stock\Models\Stock;
use App\Modules\Stock\Services\StockValidationService;
use App\Modules\Coupons\UseCases\CouponsUseCase;
use App\Modules\Email\Services\EmailService;
use App\Modules\Email\DTOs\OrderConfirmationEmailDTO;
use Illuminate\Support\Facades\DB;

class OrdersUseCase
{
    public function __construct(
        private ShippingService $shippingService,
        private CouponsUseCase $couponsUseCase,
        private EmailService $emailService,
        private SessionService $sessionService,
        private StockValidationService $stockValidationService
    ) {}

    public function createOrder(CreateOrderDTO $dto): OrderDTO
    {
        return DB::transaction(function () use ($dto) {
            $sessionId = $this->sessionService->getCurrentId();
            
            $cartItems = CartItem::with('product')
                ->where('session_id', $sessionId)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \InvalidArgumentException(ResponseMessage::ORDER_EMPTY_CART->get());
            }

            $subtotal = $cartItems->sum(fn($item) => $item->getSubtotal());
            $shippingCost = $this->shippingService->calculateShipping($subtotal);
            $discount = 0;
            $couponData = null;

            if ($dto->couponCode) {
                try {
                    $couponData = $this->couponsUseCase->applyCoupon($dto->couponCode, $subtotal);
                    $discount = $couponData['discount'];
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException(ResponseMessage::COUPON_INVALID_WITH_REASON->get(['reason' => $e->getMessage()]));
                }
            }

            $total = $subtotal - $discount + $shippingCost;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $dto->customerName ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_NAME->get(),
                'customer_email' => $dto->customerEmail ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_EMAIL->get(),
                'customer_phone' => $dto->customerPhone,
                'customer_cpf' => $dto->customerCpf,
                'customer_cep' => $dto->customerCep ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_CEP->get(),
                'customer_address' => $dto->customerAddress ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_ADDRESS->get(),
                'customer_complement' => $dto->customerComplement,
                'customer_neighborhood' => $dto->customerNeighborhood ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_NEIGHBORHOOD->get(),
                'customer_city' => $dto->customerCity ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_CITY->get(),
                'customer_state' => $dto->customerState ?: ResponseMessage::ORDER_DEFAULT_CUSTOMER_STATE->get(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'coupon_code' => $dto->couponCode,
                'coupon_id' => $couponData ? $couponData['coupon_id'] : null,
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

            $this->sendOrderConfirmationEmail($order);

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
        
        if ($status === 'cancelled') {
            if (!$order->canBeCancelled()) {
                throw new \InvalidArgumentException(ResponseMessage::ORDER_CANNOT_CANCEL->get(['status' => $order->status]));
            }
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


    private function releaseStockReservations(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->stockValidationService->releaseStock($item->product_id, $item->quantity, $item->variations);
        }
    }

    private function sendOrderConfirmationEmail(Order $order): void
    {
        $items = $order->items->map(function ($item) {
            return [
                'productName' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'variations' => $item->variations
            ];
        })->toArray();

        $emailDTO = new OrderConfirmationEmailDTO(
            orderNumber: $order->order_number,
            customerName: $order->customer_name,
            customerEmail: $order->customer_email,
            customerPhone: $order->customer_phone ?: '',
            customerCpf: $order->customer_cpf ?: '',
            customerCep: $order->customer_cep,
            customerAddress: $order->customer_address,
            customerComplement: $order->customer_complement ?: '',
            customerNeighborhood: $order->customer_neighborhood,
            customerCity: $order->customer_city,
            customerState: $order->customer_state,
            items: $items,
            subtotal: $order->subtotal,
            discount: $order->discount,
            couponCode: $order->coupon_code,
            shippingCost: $order->shipping_cost,
            total: $order->total,
            createdAt: $order->created_at
        );

        $this->emailService->sendOrderConfirmationEmail($emailDTO);
    }
}