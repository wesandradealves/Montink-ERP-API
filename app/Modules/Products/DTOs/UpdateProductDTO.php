<?php

namespace App\Modules\Products\DTOs;

class UpdateProductDTO
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

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'description' => $this->description,
            'active' => $this->active,
            'variations' => $this->variations,
        ], fn($value) => $value !== null);
    }
}