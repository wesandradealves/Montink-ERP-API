<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Modules\Stock\Services\StockValidationService;
use App\Modules\Stock\Models\Stock;
use App\Modules\Products\Models\Product;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Common\Enums\ResponseMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockValidationServiceTest extends TestCase
{
    private StockValidationService $service;
    private Product $product;
    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new StockValidationService();
        
        $this->product = Product::create([
            'name' => 'Stock Test Product',
            'sku' => 'STOCK-TEST-' . time(),
            'price' => 100.00,
            'active' => true
        ]);
        
        $this->stock = Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 100,
            'reserved' => 10,
            'variations' => null
        ]);
    }

    public function test_can_validate_available_stock(): void
    {
        $this->service->validateStock(
            $this->product,
            50,
            null
        );
        
        $this->assertTrue(true);
    }

    public function test_validation_fails_for_insufficient_stock(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(ResponseMessage::STOCK_INSUFFICIENT_AVAILABLE->get(['available' => 90]));
        
        $this->service->validateStock(
            $this->product,
            100,
            null
        );
    }

    public function test_can_get_available_quantity(): void
    {
        $available = $this->service->getAvailableQuantity(
            $this->product->id,
            null
        );
        
        $this->assertEquals(90, $available);
    }

    public function test_can_reserve_stock(): void
    {
        $this->service->reserveStock(
            $this->product->id,
            20,
            null
        );
        
        $this->stock->refresh();
        $this->assertEquals(30, $this->stock->reserved);
    }

    public function test_cannot_reserve_more_than_available(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->service->validateStock(
            $this->product,
            100,
            null
        );
    }

    public function test_can_release_stock(): void
    {
        $this->service->releaseStock(
            $this->product->id,
            5,
            null
        );
        
        $this->stock->refresh();
        $this->assertEquals(5, $this->stock->reserved);
    }

    public function test_cannot_release_more_than_reserved(): void
    {
        $this->service->releaseStock(
            $this->product->id,
            20,
            null
        );
        
        $this->stock->refresh();
        $this->assertLessThanOrEqual(0, $this->stock->reserved);
    }

    public function test_stock_validation_with_variations(): void
    {
        $productWithVar = Product::create([
            'name' => 'Product with Variations',
            'sku' => 'VAR-TEST-' . time(),
            'price' => 50.00,
            'active' => true,
            'variations' => [
                ['size' => 'M', 'color' => 'Blue']
            ]
        ]);
        
        Stock::create([
            'product_id' => $productWithVar->id,
            'quantity' => 50,
            'reserved' => 5,
            'variations' => ['size' => 'M', 'color' => 'Blue']
        ]);
        
        $this->service->validateStock(
            $productWithVar,
            10,
            ['size' => 'M', 'color' => 'Blue']
        );
        
        $this->assertTrue(true);
    }

    public function test_returns_zero_for_non_existent_stock(): void
    {
        $nonExistentProductId = 99999;
        
        $available = $this->service->getAvailableQuantity(
            $nonExistentProductId,
            null
        );
        
        $this->assertEquals(0, $available);
    }

    public function test_multiple_stock_operations(): void
    {
        $this->service->reserveStock($this->product->id, 20, null);
        $this->service->reserveStock($this->product->id, 10, null);
        $this->service->releaseStock($this->product->id, 5, null);
        
        $this->stock->refresh();
        $this->assertEquals(35, $this->stock->reserved);
        
        $available = $this->service->getAvailableQuantity($this->product->id, null);
        $this->assertEquals(65, $available);
    }

    public function test_validation_fails_when_stock_not_found(): void
    {
        $productWithoutStock = Product::create([
            'name' => 'Product Without Stock',
            'sku' => 'NO-STOCK-' . time(),
            'price' => 50.00,
            'active' => true
        ]);
        
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage(ResponseMessage::PRODUCT_STOCK_NOT_FOUND->get());
        
        $this->service->validateStock(
            $productWithoutStock,
            1,
            null
        );
    }
    
    public function test_validation_requires_variation_when_product_has_variations(): void
    {
        $productWithVar = Product::create([
            'name' => 'Product Requiring Variations',
            'sku' => 'VAR-REQ-' . time(),
            'price' => 75.00,
            'active' => true
        ]);
        
        Stock::create([
            'product_id' => $productWithVar->id,
            'quantity' => 50,
            'reserved' => 0,
            'variations' => ['size' => 'L']
        ]);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(ResponseMessage::PRODUCT_VARIATION_REQUIRED->get());
        
        $this->service->validateStock(
            $productWithVar,
            1,
            null
        );
    }
    
    protected function tearDown(): void
    {
        Stock::where('product_id', $this->product->id)->delete();
        $this->product->delete();
        
        parent::tearDown();
    }
}