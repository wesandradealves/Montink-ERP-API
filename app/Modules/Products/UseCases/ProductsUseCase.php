<?php

namespace App\Modules\Products\UseCases;

use App\Common\Exceptions\ResourceNotFoundException;
use App\Modules\Products\DTOs\CreateProductDTO;
use App\Modules\Products\DTOs\UpdateProductDTO;
use App\Modules\Products\Models\Product;
use App\Modules\Products\Repositories\ProductRepositoryInterface;

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


        return $this->productRepository->create($dto->toArray());
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


        return $this->productRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        if (!$this->productRepository->exists($id)) {
            throw new ResourceNotFoundException('Produto');
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