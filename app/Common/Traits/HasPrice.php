<?php

namespace App\Common\Traits;

trait HasPrice
{
    protected function initializeHasPrice(): void
    {
        $this->casts = array_merge($this->casts, [
            'price' => 'decimal:2',
        ]);
    }
}