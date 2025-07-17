<?php

namespace App\Modules\Products\DTOs;

use App\Common\Base\BaseDTO;

class CreateProductDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $sku,
        public readonly float $price,
        public readonly ?string $description = null,
        public readonly bool $active = true,
        public readonly ?array $variations = null,
    ) {
    }

}