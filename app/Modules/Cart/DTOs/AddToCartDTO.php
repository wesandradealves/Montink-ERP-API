<?php

namespace App\Modules\Cart\DTOs;

use App\Common\Base\BaseDTO;

class AddToCartDTO extends BaseDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly ?array $variations = null,
    ) {}
}