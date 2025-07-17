<?php

namespace App\Modules\Products\DTOs;

class CreateProductDTO
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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'description' => $this->description,
            'active' => $this->active,
            'variations' => $this->variations,
        ];
    }
}