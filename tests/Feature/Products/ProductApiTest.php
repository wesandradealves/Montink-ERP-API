<?php

namespace Tests\Feature\Products;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Product::create([
            'name' => 'Product 1',
            'sku' => 'PROD-001',
            'price' => 100.00,
            'active' => true
        ]);
        
        Product::create([
            'name' => 'Product 2',
            'sku' => 'PROD-002', 
            'price' => 200.00,
            'active' => true
        ]);
        
        Product::create([
            'name' => 'Inactive Product',
            'sku' => 'PROD-003',
            'price' => 300.00,
            'active' => false
        ]);
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
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_products_by_active_status(): void
    {
        $response = $this->getJson('/api/products?only_active=true');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
        
        foreach ($response->json('data') as $product) {
            $this->assertTrue($product['active']);
        }
    }

    public function test_can_search_products_by_name(): void
    {
        $response = $this->getJson('/api/products?search=Product 1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Product 1');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $response = $this->getJson('/api/products?min_price=150&max_price=250');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.price', '200.00');
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::first();
        
        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price
                ],
                'message' => 'Produto encontrado com sucesso'
            ]);
    }

    public function test_returns_404_for_nonexistent_product(): void
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Produto não encontrado'
            ]);
    }

    public function test_can_create_product(): void
    {
        $productData = [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 99.99,
            'sku' => 'NEW-001',
            'active' => true
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product')
            ->assertJsonPath('data.sku', 'NEW-001')
            ->assertJsonPath('message', 'Produto criado com sucesso');

        $this->assertDatabaseHas('products', [
            'sku' => 'NEW-001',
            'name' => 'New Product'
        ]);
    }

    public function test_create_product_with_variations(): void
    {
        $productData = [
            'name' => 'T-Shirt',
            'price' => 29.99,
            'sku' => 'TSHIRT-001',
            'variations' => [
                ['size' => 'S', 'color' => 'Blue'],
                ['size' => 'M', 'color' => 'Blue'],
                ['size' => 'L', 'color' => 'Blue']
            ]
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonPath('data.variations', $productData['variations']);

        $product = Product::where('sku', 'TSHIRT-001')->first();
        $this->assertCount(3, $product->stocks);
    }

    public function test_cannot_create_product_with_duplicate_sku(): void
    {
        $productData = [
            'name' => 'Duplicate Product',
            'price' => 99.99,
            'sku' => 'PROD-001'
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Este SKU já está em uso'
            ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::first();
        
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 149.99
        ];

        $response = $this->patchJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Product Name')
            ->assertJsonPath('data.price', '149.99')
            ->assertJsonPath('message', 'Produto atualizado com sucesso');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name'
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::first();
        
        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => null,
                'message' => 'Produto excluído com sucesso'
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    public function test_create_product_validation(): void
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'sku']);

        $response = $this->postJson('/api/products', [
            'name' => 'a',
            'price' => -10,
            'sku' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'sku']);
    }

    public function test_product_price_must_be_positive(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }
}