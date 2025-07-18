<?php

namespace App\Modules\Cart\DTOs;

use App\Common\Base\BaseDTO;

class CartItemDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $productName,
        public readonly int $quantity,
        public readonly float $price,
        public readonly float $subtotal,
        public readonly ?array $variations = null,
    ) {}
}