<?php

namespace App\Modules\Stock\Services;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;

class StockValidationService
{
    public function validateStock(Product $product, int $quantity, ?array $variations = null): void
    {
        $stock = Stock::where('product_id', $product->id);
        
        if ($variations) {
            $stock->whereJsonContains('variations', $variations);
        } else {
            $stock->whereNull('variations');
        }
        
        $stock = $stock->first();
        
        if (!$stock) {
            if ($variations === null && Stock::where('product_id', $product->id)->whereNotNull('variations')->exists()) {
                throw new \InvalidArgumentException(ResponseMessage::PRODUCT_VARIATION_REQUIRED->get());
            }
            throw new ResourceNotFoundException(ResponseMessage::PRODUCT_STOCK_NOT_FOUND->get());
        }
        
        $available = $stock->quantity - $stock->reserved;
        
        if ($available < $quantity) {
            throw new \InvalidArgumentException(ResponseMessage::STOCK_INSUFFICIENT_AVAILABLE->get(['available' => $available]));
        }
    }

    public function getAvailableQuantity(int $productId, ?array $variations = null): int
    {
        $stock = Stock::where('product_id', $productId);
        
        if ($variations) {
            $stock->whereJsonContains('variations', $variations);
        } else {
            $stock->whereNull('variations');
        }
        
        $stock = $stock->first();
        
        if (!$stock) {
            return 0;
        }
        
        return $stock->quantity - $stock->reserved;
    }

    public function reserveStock(int $productId, int $quantity, ?array $variations = null): void
    {
        $stock = Stock::where('product_id', $productId);
        
        if ($variations) {
            $stock->whereJsonContains('variations', $variations);
        } else {
            $stock->whereNull('variations');
        }
        
        $stock = $stock->first();
        
        if ($stock) {
            $stock->increment('reserved', $quantity);
        }
    }

    public function releaseStock(int $productId, int $quantity, ?array $variations = null): void
    {
        $stock = Stock::where('product_id', $productId);
        
        if ($variations) {
            $stock->whereJsonContains('variations', $variations);
        } else {
            $stock->whereNull('variations');
        }
        
        $stock = $stock->first();
        
        if ($stock) {
            $stock->decrement('reserved', $quantity);
        }
    }
}