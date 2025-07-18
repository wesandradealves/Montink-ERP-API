<?php

namespace App\Modules\Cart\DTOs;

class AddToCartDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly ?array $variations = null,
    ) {}
}