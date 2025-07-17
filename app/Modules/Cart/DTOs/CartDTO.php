<?php

namespace App\Modules\Cart\DTOs;

use App\Common\Base\BaseDTO;

class CartDTO extends BaseDTO
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