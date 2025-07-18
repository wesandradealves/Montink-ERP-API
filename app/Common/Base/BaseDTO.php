<?php

namespace App\Common\Base;

abstract class BaseDTO
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}