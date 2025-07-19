<?php

namespace Tests\Feature\Cart;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    private Product $product1;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->product1 = Product::create([
            'name' => 'Product 1',
            'sku' => 'PROD-001',
            'price' => 100.00,
            'active' => true
        ]);

        Stock::create([
            'product_id' => $this->product1->id,
            'quantity' => 100,
            'reserved' => 0
        ]);
        
        $this->product2 = Product::create([
            'name' => 'Product 2',
            'sku' => 'PROD-002',
            'price' => 50.00,
            'active' => true
        ]);

        Stock::create([
            'product_id' => $this->product2->id,
            'quantity' => 50,
            'reserved' => 0
        ]);
    }

    public function test_can_add_product_to_cart(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produto adicionado ao carrinho com sucesso'
            ]);

        $cartResponse = $this->getJson('/api/cart');
        $cartResponse->assertJsonCount(1, 'data.items');
    }

    public function test_can_view_cart(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $this->postJson('/api/cart', [
            'product_id' => $this->product2->id,
            'quantity' => 1
        ]);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'product_id',
                            'quantity',
                            'product' => [
                                'id',
                                'name',
                                'price',
                                'sku'
                            ],
                            'subtotal'
                        ]
                    ],
                    'subtotal',
                    'discount',
                    'total'
                ]
            ])
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.subtotal', 250.00)
            ->assertJsonPath('data.total', 250.00);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $cartResponse = $this->getJson('/api/cart');
        $itemId = $cartResponse->json('data.items.0.id');

        $response = $this->patchJson("/api/cart/{$itemId}", [
            'quantity' => 5
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Quantidade atualizada no carrinho'
            ]);

        $updatedCart = $this->getJson('/api/cart');
        $updatedCart->assertJsonPath('data.items.0.quantity', 5);
    }

    public function test_can_remove_item_from_cart(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $cartResponse = $this->getJson('/api/cart');
        $itemId = $cartResponse->json('data.items.0.id');

        $response = $this->deleteJson("/api/cart/{$itemId}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produto removido do carrinho'
            ]);

        $emptyCart = $this->getJson('/api/cart');
        $emptyCart->assertJsonCount(0, 'data.items');
    }

    public function test_can_clear_entire_cart(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $this->postJson('/api/cart', [
            'product_id' => $this->product2->id,
            'quantity' => 1
        ]);

        $response = $this->deleteJson('/api/cart');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Carrinho limpo com sucesso'
            ]);

        $emptyCart = $this->getJson('/api/cart');
        $emptyCart->assertJsonCount(0, 'data.items')
            ->assertJsonPath('data.total', 0);
    }

    public function test_cannot_add_product_with_insufficient_stock(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 150
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Estoque insuficiente. Disponível: 100'
            ]);
    }

    public function test_cannot_add_inactive_product_to_cart(): void
    {
        $inactiveProduct = Product::create([
            'name' => 'Inactive Product',
            'sku' => 'INACTIVE-001',
            'price' => 100.00,
            'active' => false
        ]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $inactiveProduct->id,
            'quantity' => 1
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Produto não encontrado'
            ]);
    }

    public function test_can_add_product_with_variations_to_cart(): void
    {
        $productWithVariations = Product::create([
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-001',
            'price' => 29.99,
            'active' => true,
            'variations' => [
                ['size' => 'M', 'color' => 'Blue']
            ]
        ]);

        Stock::create([
            'product_id' => $productWithVariations->id,
            'variations' => ['size' => 'M', 'color' => 'Blue'],
            'quantity' => 10,
            'reserved' => 0
        ]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $productWithVariations->id,
            'quantity' => 2,
            'variations' => ['size' => 'M', 'color' => 'Blue']
        ]);

        $response->assertStatus(200);
    }

    public function test_cart_persists_across_requests_with_session(): void
    {
        $response1 = $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 1
        ]);

        $cookies = $response1->headers->getCookies();
        $sessionCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'laravel_session') {
                $sessionCookie = $cookie->getValue();
                break;
            }
        }

        $response2 = $this->withCookie('laravel_session', $sessionCookie)
            ->getJson('/api/cart');

        $response2->assertStatus(200)
            ->assertJsonCount(1, 'data.items');
    }

    public function test_cart_calculates_subtotal_correctly(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $this->postJson('/api/cart', [
            'product_id' => $this->product2->id,
            'quantity' => 3
        ]);

        $response = $this->getJson('/api/cart');

        $expectedSubtotal = (100.00 * 2) + (50.00 * 3);
        
        $response->assertJsonPath('data.subtotal', $expectedSubtotal)
            ->assertJsonPath('data.items.0.subtotal', 200.00)
            ->assertJsonPath('data.items.1.subtotal', 150.00);
    }
}