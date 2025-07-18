<?php

namespace App\Modules\Orders\DTOs;

use App\Common\Base\BaseDTO;
use App\Modules\Orders\Models\Order;

class OrderDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $orderNumber,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly ?string $customerCpf,
        public readonly string $customerCep,
        public readonly string $customerAddress,
        public readonly ?string $customerComplement,
        public readonly string $customerNeighborhood,
        public readonly string $customerCity,
        public readonly string $customerState,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly string $status,
        public readonly ?string $couponCode,
        public readonly array $items,
        public readonly string $createdAt,
    ) {}

    public static function fromModel(Order $order): self
    {
        $items = $order->items->map(function ($item) {
            return OrderItemDTO::fromModel($item);
        })->toArray();

        return new self(
            id: $order->id,
            orderNumber: $order->order_number,
            customerName: $order->customer_name,
            customerEmail: $order->customer_email,
            customerPhone: $order->customer_phone,
            customerCpf: $order->customer_cpf,
            customerCep: $order->customer_cep,
            customerAddress: $order->customer_address,
            customerComplement: $order->customer_complement,
            customerNeighborhood: $order->customer_neighborhood,
            customerCity: $order->customer_city,
            customerState: $order->customer_state,
            subtotal: (float) $order->subtotal,
            discount: (float) $order->discount,
            shippingCost: (float) $order->shipping_cost,
            total: (float) $order->total,
            status: $order->status,
            couponCode: $order->coupon_code,
            items: $items,
            createdAt: $order->created_at->format('Y-m-d H:i:s'),
        );
    }
}