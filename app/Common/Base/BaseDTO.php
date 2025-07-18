<?php

namespace App\Common\Base;

abstract class BaseDTO
{
    /**
     * Converte o DTO para array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Converte o DTO para JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Converte o DTO para string (JSON)
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Remove valores nulos do array
     */
    public function toArrayWithoutNulls(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null);
    }

    /**
     * Verifica se uma propriedade específica está definida e não é nula
     */
    public function has(string $property): bool
    {
        return property_exists($this, $property) && $this->$property !== null;
    }

    /**
     * Obtém o valor de uma propriedade ou retorna um padrão
     */
    public function get(string $property, mixed $default = null): mixed
    {
        return $this->has($property) ? $this->$property : $default;
    }

    /**
     * Cria uma nova instância do DTO a partir de um array
     */
    public static function fromArray(array $data): static
    {
        $reflection = new \ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return new static();
        }

        $parameters = [];
        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();
            $parameters[] = $data[$name] ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
        }

        return new static(...$parameters);
    }

    /**
     * Valida se todos os campos obrigatórios estão preenchidos
     */
    public function validate(): array
    {
        $errors = [];
        $reflection = new \ReflectionClass($this);
        
        foreach ($reflection->getProperties() as $property) {
            if (!$property->isInitialized($this)) {
                $errors[] = "O campo {$property->getName()} é obrigatório";
            }
        }

        return $errors;
    }
}