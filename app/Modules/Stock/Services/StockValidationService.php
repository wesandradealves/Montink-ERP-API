<?php

namespace App\Modules\Stock\Services;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Common\Traits\FindsResources;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;

class StockValidationService
{
    use FindsResources;
    
    /**
     * Busca stock por produto e variações
     */
    private function findStock(int $productId, ?array $variations = null): ?Stock
    {
        $query = Stock::where('product_id', $productId);
        
        if ($variations) {
            $query->whereJsonContains('variations', $variations);
        } else {
            $query->whereNull('variations');
        }
        
        return $query->first();
    }
    public function validateStock(Product $product, int $quantity, ?array $variations = null): void
    {
        $stock = $this->findStock($product->id, $variations);
        
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
        $stock = $this->findStock($productId, $variations);
        
        if (!$stock) {
            return 0;
        }
        
        return $stock->quantity - $stock->reserved;
    }

    public function reserveStock(int $productId, int $quantity, ?array $variations = null): void
    {
        $stock = $this->findStock($productId, $variations);
        
        if ($stock) {
            $stock->increment('reserved', $quantity);
        }
    }

    public function releaseStock(int $productId, int $quantity, ?array $variations = null): void
    {
        $stock = $this->findStock($productId, $variations);
        
        if ($stock) {
            $stock->decrement('reserved', $quantity);
        }
    }
}