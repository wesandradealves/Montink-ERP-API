<?php

namespace App\Domain\Interfaces\Repositories;

interface BaseRepositoryInterface
{
    public function find(int $id): ?object;
    
    public function findAll(): array;
    
    public function findBy(array $criteria): array;
    
    public function findOneBy(array $criteria): ?object;
    
    public function create(array $data): object;
    
    public function update(int $id, array $data): object;
    
    public function delete(int $id): bool;
    
    public function exists(int $id): bool;
    
    public function count(): int;
}