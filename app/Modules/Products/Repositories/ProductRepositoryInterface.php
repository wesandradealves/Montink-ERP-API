<?php

namespace App\Modules\Products\Repositories;

use App\Domain\Interfaces\Repositories\BaseRepositoryInterface;
use App\Modules\Products\Models\Product;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySku(string $sku): ?Product;
    
    public function findActiveProducts(): array;
    
    public function searchByName(string $name): array;
}