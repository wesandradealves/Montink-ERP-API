<?php

namespace App\Modules\Orders\DTOs;

use App\Common\Base\BaseDTO;
use App\Modules\Orders\Models\OrderItem;

class OrderItemDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $productName,
        public readonly string $productSku,
        public readonly int $quantity,
        public readonly float $price,
        public readonly float $subtotal,
        public readonly ?array $variations,
    ) {}

    public static function fromModel(OrderItem $item): self
    {
        return new self(
            id: $item->id,
            productId: $item->product_id,
            productName: $item->product_name,
            productSku: $item->product_sku,
            quantity: $item->quantity,
            price: (float) $item->price,
            subtotal: (float) $item->subtotal,
            variations: $item->variations,
        );
    }
}