<?php

namespace App\Common\Base;

use App\Common\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseUseCase
{
    protected function findOrFail(string $modelClass, int $id, string $resourceName = null): Model
    {
        $model = $modelClass::find($id);
        
        if (!$model) {
            $resourceName = $resourceName ?? class_basename($modelClass);
            throw new ResourceNotFoundException($resourceName, $id);
        }
        
        return $model;
    }
    
    protected function executeInTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }
    
    protected function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }
    }
    
    protected function applyPagination($query, ?int $perPage = null, ?string $sortBy = 'id', ?string $sortDirection = 'desc')
    {
        $query->orderBy($sortBy, $sortDirection);
        
        if ($perPage) {
            return $query->paginate($perPage);
        }
        
        return $query->get();
    }
}