<?php

namespace App\Common\Traits;

use App\Common\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;

trait FindsResources
{
    /**
     * Busca recurso por ID ou lança exceção
     */
    protected function findOrFail(string $modelClass, $id, string $errorMessage): Model
    {
        $resource = $modelClass::find($id);
        
        if (!$resource) {
            throw new ResourceNotFoundException($errorMessage);
        }
        
        return $resource;
    }
    
    /**
     * Busca recurso por campo específico ou lança exceção
     */
    protected function findByOrFail(string $modelClass, string $field, $value, string $errorMessage): Model
    {
        $resource = $modelClass::where($field, $value)->first();
        
        if (!$resource) {
            throw new ResourceNotFoundException($errorMessage);
        }
        
        return $resource;
    }
}