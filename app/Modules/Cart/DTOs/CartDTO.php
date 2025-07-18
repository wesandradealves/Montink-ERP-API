<?php

namespace App\Modules\Cart\DTOs;

class CartDTO
{
    public function __construct(
        public readonly array $items,
        public readonly float $subtotal,
        public readonly int $totalItems,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly string $shippingDescription,
    ) {}
}