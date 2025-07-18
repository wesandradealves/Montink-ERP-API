<?php

namespace App\Common\Traits;

trait HasVariations
{
    protected function initializeHasVariations(): void
    {
        $this->casts = array_merge($this->casts, [
            'variations' => 'array',
        ]);
    }
}