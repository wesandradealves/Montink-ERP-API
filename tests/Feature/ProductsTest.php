<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Common\Enums\ResponseMessage;

class ProductsTest extends TestCase
{
    private Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->testProduct = Product::firstOrCreate(
            ['sku' => 'TEST-PRODUCT-001'],
            [
                'name' => 'Test Product',
                'description' => 'Product for testing',
                'price' => 100.00,
                'active' => true
            ]
        );

        Stock::firstOrCreate(
            ['product_id' => $this->testProduct->id],
            [
                'quantity' => 100,
                'reserved' => 0,
                'variations' => null
            ]
        );
    }

    public function test_can_list_all_products(): void
    {
        $response = $this->getJson('/api/products');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'sku',
                        'active',
                        'variations',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_filter_products_by_active_status(): void
    {
        $response = $this->getJson('/api/products?only_active=true');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertTrue($product['active']);
        }
    }

    public function test_can_search_products_by_name(): void
    {
        $response = $this->getJson('/api/products?search=Test');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        if (count($products) > 0) {
            foreach ($products as $product) {
                $this->assertStringContainsStringIgnoringCase('test', $product['name']);
            }
        }
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $response = $this->getJson('/api/products?min_price=50&max_price=150');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        foreach ($products as $product) {
            $price = floatval($product['price']);
            $this->assertGreaterThanOrEqual(50, $price);
            $this->assertLessThanOrEqual(150, $price);
        }
    }

    public function test_can_filter_products_by_min_price_only(): void
    {
        $response = $this->getJson('/api/products?min_price=200');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        foreach ($products as $product) {
            $price = floatval($product['price']);
            $this->assertGreaterThanOrEqual(200, $price);
        }
    }

    public function test_can_filter_products_by_max_price_only(): void
    {
        $response = $this->getJson('/api/products?max_price=50');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        foreach ($products as $product) {
            $price = floatval($product['price']);
            $this->assertLessThanOrEqual(50, $price);
        }
    }

    public function test_can_combine_filters(): void
    {
        $response = $this->getJson('/api/products?only_active=true&min_price=10&max_price=200');
        
        $response->assertStatus(200);
        
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertTrue($product['active']);
            $price = floatval($product['price']);
            $this->assertGreaterThanOrEqual(10, $price);
            $this->assertLessThanOrEqual(200, $price);
        }
    }

    public function test_can_get_single_product(): void
    {
        $response = $this->getJson("/api/products/{$this->testProduct->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'sku',
                    'active'
                ],
                'message'
            ])
            ->assertJsonPath('data.id', $this->testProduct->id)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_returns_404_for_non_existent_product(): void
    {
        $response = $this->getJson('/api/products/999999');
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_can_create_product(): void
    {
        $productData = [
            'name' => 'New Test Product',
            'description' => 'A new product for testing',
            'price' => 299.99,
            'sku' => 'NEW-TEST-' . time(),
            'active' => true
        ];
        
        $response = $this->postJson('/api/products', $productData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'sku',
                    'price'
                ],
                'message'
            ])
            ->assertJsonPath('data.name', 'New Test Product')
            ->assertJsonPath('message', ResponseMessage::PRODUCT_CREATED->get());
    }

    public function test_cannot_create_product_with_duplicate_sku(): void
    {
        $productData = [
            'name' => 'Duplicate Product',
            'price' => 99.99,
            'sku' => $this->testProduct->sku
        ];
        
        $response = $this->postJson('/api/products', $productData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    }

    public function test_create_product_validation(): void
    {
        $response = $this->postJson('/api/products', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'sku']);
    }

    public function test_can_update_product(): void
    {
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 149.99
        ];
        
        $response = $this->patchJson("/api/products/{$this->testProduct->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Product Name')
            ->assertJsonPath('data.price', '149.99')
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_can_delete_product(): void
    {
        $product = Product::create([
            'name' => 'Product to Delete',
            'sku' => 'DELETE-TEST-' . time(),
            'price' => 99.99,
            'active' => true
        ]);
        
        $response = $this->deleteJson("/api/products/{$product->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data', null)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_create_product_with_variations(): void
    {
        $productData = [
            'name' => 'T-Shirt with Variations',
            'price' => 29.99,
            'sku' => 'TSHIRT-VAR-' . time(),
            'active' => true,
            'variations' => [
                ['size' => 'S', 'color' => 'Blue'],
                ['size' => 'M', 'color' => 'Blue'],
                ['size' => 'L', 'color' => 'Blue']
            ]
        ];
        
        $response = $this->postJson('/api/products', $productData);
        
        $response->assertStatus(201)
            ->assertJsonCount(3, 'data.variations');
    }
}