<?php

namespace App\Modules\Products\DTOs;

use App\Common\Base\BaseDTO;

class UpdateProductDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $sku = null,
        public readonly ?float $price = null,
        public readonly ?string $description = null,
        public readonly ?bool $active = null,
        public readonly ?array $variations = null,
    ) {
    }


}