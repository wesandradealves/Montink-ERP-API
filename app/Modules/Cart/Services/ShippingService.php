<?php

namespace App\Modules\Cart\Services;

class ShippingService
{
    public function calculateShipping(float $subtotal): float
    {
        if ($subtotal >= 200.00) {
            return 0.00;
        }
        
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        }
        
        return 20.00;
    }

    public function getShippingDescription(float $subtotal): string
    {
        $shippingCost = $this->calculateShipping($subtotal);
        
        if ($shippingCost === 0.00) {
            return 'Frete grátis';
        }
        
        return "Frete: R$ " . number_format($shippingCost, 2, ',', '.');
    }
}