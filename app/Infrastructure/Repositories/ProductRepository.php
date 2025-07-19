<?php

namespace App\Infrastructure\Repositories;

use App\Modules\Products\Models\Product;
use App\Modules\Products\Repositories\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findAll(): array
    {
        return Product::all()->toArray();
    }

    public function findBy(array $criteria): array
    {
        return $this->buildQuery($criteria)->get()->toArray();
    }

    public function findOneBy(array $criteria): ?Product
    {
        return $this->buildQuery($criteria)->first();
    }

    private function buildQuery(array $criteria)
    {
        $query = Product::query();
        
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        
        return $product->fresh();
    }

    public function delete(int $id): bool
    {
        $product = Product::find($id);
        
        if (!$product) {
            return false;
        }
        
        return $product->delete();
    }

    public function exists(int $id): bool
    {
        return Product::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Product::count();
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function findActiveProducts(): array
    {
        return Product::where('active', true)->get()->toArray();
    }

    public function searchByName(string $name): array
    {
        return Product::where('name', 'LIKE', "%{$name}%")->get()->toArray();
    }
    
    public function findByPriceRange(?float $minPrice, ?float $maxPrice, bool $onlyActive = false): array
    {
        $query = Product::query();
        
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        
        if ($onlyActive) {
            $query->where('active', true);
        }
        
        return $query->get()->toArray();
    }
}