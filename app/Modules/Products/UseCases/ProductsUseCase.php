<?php

namespace App\Modules\Products\UseCases;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Modules\Products\DTOs\CreateProductDTO;
use App\Modules\Products\DTOs\UpdateProductDTO;
use App\Modules\Products\Models\Product;
use App\Modules\Products\Repositories\ProductRepositoryInterface;
use App\Modules\Stock\Models\Stock;

class ProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function create(array $data): Product
    {
        $dto = new CreateProductDTO(
            name: $data['name'],
            sku: $data['sku'],
            price: $data['price'],
            description: $data['description'] ?? null,
            active: $data['active'] ?? true,
            variations: $data['variations'] ?? null,
        );


        $product = $this->productRepository->create($dto->toArray());
        
        if ($product->variations && is_array($product->variations)) {
            foreach ($product->variations as $variation) {
                Stock::create([
                    'product_id' => $product->id,
                    'variations' => $variation,
                    'quantity' => 100,
                    'reserved' => 0
                ]);
            }
        } else {
            Stock::create([
                'product_id' => $product->id,
                'variations' => null,
                'quantity' => 100,
                'reserved' => 0
            ]);
        }
        
        return $product;
    }

    public function update(int $id, array $data): Product
    {
        $dto = new UpdateProductDTO(
            name: $data['name'] ?? null,
            sku: $data['sku'] ?? null,
            price: $data['price'] ?? null,
            description: $data['description'] ?? null,
            active: $data['active'] ?? null,
            variations: $data['variations'] ?? null,
        );


        return $this->productRepository->update($id, $dto->toArrayWithoutNulls());
    }

    public function delete(int $id): bool
    {
        if (!$this->productRepository->exists($id)) {
            throw new ResourceNotFoundException(ResponseMessage::PRODUCT_NOT_FOUND->get());
        }

        return $this->productRepository->delete($id);
    }

    public function find(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function list(array $filters = []): array
    {
        $onlyActive = $filters['only_active'] ?? false;
        $search = $filters['search'] ?? null;
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;
        
        if ($minPrice !== null || $maxPrice !== null) {
            return $this->productRepository->findByPriceRange($minPrice, $maxPrice, $onlyActive);
        }
        
        if ($search) {
            return $this->productRepository->searchByName($search);
        }
        
        if ($onlyActive) {
            return $this->productRepository->findActiveProducts();
        }
        
        return $this->productRepository->findAll();
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->productRepository->findBySku($sku);
    }
}