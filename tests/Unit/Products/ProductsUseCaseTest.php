<?php

namespace Tests\Unit\Products;

use Tests\TestCase;
use App\Modules\Products\UseCases\ProductsUseCase;
use App\Modules\Products\Repositories\ProductRepositoryInterface;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Common\Exceptions\BusinessException;
use App\Common\Exceptions\ResourceNotFoundException;
use Mockery;

class ProductsUseCaseTest extends TestCase
{
    private ProductsUseCase $productsUseCase;
    private $productRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->productRepositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->productsUseCase = new ProductsUseCase($this->productRepositoryMock);
    }

    public function test_create_product_successfully(): void
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'description' => 'Test Description',
            'active' => true
        ];

        $product = new Product($productData);
        $product->id = 1;

        $this->productRepositoryMock
            ->shouldReceive('findBySku')
            ->with('TEST-001')
            ->once()
            ->andReturn(null);

        $this->productRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($product);

        $result = $this->productsUseCase->create($productData);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Test Product', $result->name);
        $this->assertEquals('TEST-001', $result->sku);
    }

    public function test_create_product_with_duplicate_sku_fails(): void
    {
        $this->expectException(BusinessException::class);

        $productData = [
            'name' => 'Test Product',
            'sku' => 'EXISTING-SKU',
            'price' => 99.99
        ];

        $existingProduct = new Product(['sku' => 'EXISTING-SKU']);

        $this->productRepositoryMock
            ->shouldReceive('findBySku')
            ->with('EXISTING-SKU')
            ->once()
            ->andReturn($existingProduct);

        $this->productsUseCase->create($productData);
    }

    public function test_update_product_successfully(): void
    {
        $productId = 1;
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99
        ];

        $existingProduct = new Product([
            'id' => $productId,
            'name' => 'Old Product',
            'sku' => 'TEST-001',
            'price' => 99.99
        ]);

        $updatedProduct = new Product(array_merge($existingProduct->toArray(), $updateData));

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($existingProduct);

        $this->productRepositoryMock
            ->shouldReceive('update')
            ->with($productId, $updateData)
            ->once()
            ->andReturn($updatedProduct);

        $result = $this->productsUseCase->update($productId, $updateData);

        $this->assertEquals('Updated Product', $result->name);
        $this->assertEquals(149.99, $result->price);
    }

    public function test_update_nonexistent_product_fails(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with(999)
            ->once()
            ->andReturn(null);

        $this->productsUseCase->update(999, ['name' => 'Updated']);
    }

    public function test_delete_product_successfully(): void
    {
        $productId = 1;

        $product = new Product(['id' => $productId]);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($product);

        $this->productRepositoryMock
            ->shouldReceive('delete')
            ->with($productId)
            ->once()
            ->andReturn(true);

        $result = $this->productsUseCase->delete($productId);

        $this->assertTrue($result);
    }

    public function test_list_products_with_filters(): void
    {
        $activeProducts = [
            ['id' => 1, 'name' => 'Active Product 1', 'active' => true],
            ['id' => 2, 'name' => 'Active Product 2', 'active' => true]
        ];

        $this->productRepositoryMock
            ->shouldReceive('findActiveProducts')
            ->once()
            ->andReturn($activeProducts);

        $result = $this->productsUseCase->list(['only_active' => true]);

        $this->assertCount(2, $result);
    }

    public function test_list_products_with_search(): void
    {
        $searchResults = [
            ['id' => 1, 'name' => 'Notebook Dell', 'sku' => 'NB-DELL-001']
        ];

        $this->productRepositoryMock
            ->shouldReceive('searchByName')
            ->with('notebook')
            ->once()
            ->andReturn($searchResults);

        $result = $this->productsUseCase->list(['search' => 'notebook']);

        $this->assertCount(1, $result);
        $this->assertStringContainsString('Notebook', $result[0]['name']);
    }

    public function test_list_products_with_price_filter(): void
    {
        $priceFilteredProducts = [
            ['id' => 1, 'name' => 'Product 1', 'price' => 150.00],
            ['id' => 2, 'name' => 'Product 2', 'price' => 200.00]
        ];

        $this->productRepositoryMock
            ->shouldReceive('findByPriceRange')
            ->with(100.0, 250.0, false)
            ->once()
            ->andReturn($priceFilteredProducts);

        $result = $this->productsUseCase->list([
            'min_price' => 100,
            'max_price' => 250
        ]);

        $this->assertCount(2, $result);
        $this->assertGreaterThanOrEqual(100, $result[0]['price']);
        $this->assertLessThanOrEqual(250, $result[1]['price']);
    }

    public function test_find_product_by_id(): void
    {
        $productId = 1;
        $product = new Product([
            'id' => $productId,
            'name' => 'Test Product',
            'sku' => 'TEST-001'
        ]);

        $this->productRepositoryMock
            ->shouldReceive('find')
            ->with($productId)
            ->once()
            ->andReturn($product);

        $result = $this->productsUseCase->find($productId);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($productId, $result->id);
    }

    public function test_find_product_by_sku(): void
    {
        $sku = 'TEST-001';
        $product = new Product([
            'id' => 1,
            'name' => 'Test Product',
            'sku' => $sku
        ]);

        $this->productRepositoryMock
            ->shouldReceive('findBySku')
            ->with($sku)
            ->once()
            ->andReturn($product);

        $result = $this->productsUseCase->findBySku($sku);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($sku, $result->sku);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}