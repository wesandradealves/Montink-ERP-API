<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Common\Enums\ResponseMessage;

class CartTest extends TestCase
{
    private Product $product;
    private string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->product = Product::firstOrCreate(
            ['sku' => 'CART-TEST-001'],
            [
                'name' => 'Cart Test Product',
                'price' => 100.00,
                'active' => true
            ]
        );

        Stock::firstOrCreate(
            ['product_id' => $this->product->id],
            [
                'quantity' => 50,
                'reserved' => 0
            ]
        );
    }

    private function getSessionCookie($response): ?string
    {
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'session_id') {
                return $cookie->getValue();
            }
        }
        return null;
    }

    public function test_can_add_product_to_cart(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $response->assertStatus(201)
            ->assertJson([
                'message' => ResponseMessage::CART_ITEM_ADDED->get()
            ]);
        
        $this->sessionCookie = $this->getSessionCookie($response);
    }

    public function test_can_view_cart(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'productId',
                            'productName',
                            'quantity',
                            'price',
                            'subtotal',
                            'variations'
                        ]
                    ],
                    'subtotal',
                    'totalItems',
                    'shippingCost',
                    'total',
                    'shippingDescription'
                ],
                'message'
            ]);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $cartResponse = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $itemId = $cartResponse->json('data.items.0.id');
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->patchJson("/api/cart/{$itemId}", [
                'quantity' => 5
            ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => ResponseMessage::OPERATION_SUCCESS->get()
            ]);
    }

    public function test_can_remove_item_from_cart(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $cartResponse = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $itemId = $cartResponse->json('data.items.0.id');
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->deleteJson("/api/cart/{$itemId}");
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => ResponseMessage::OPERATION_SUCCESS->get()
            ]);
    }

    public function test_can_clear_entire_cart(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->deleteJson('/api/cart');
        
        $response->assertStatus(200)
            ->assertJson([
                'message' => ResponseMessage::OPERATION_SUCCESS->get()
            ]);
        
        $cartResponse = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $cartResponse->assertJsonCount(0, 'data.items')
            ->assertJsonPath('data.total', 20); // Frete mínimo quando carrinho vazio
    }

    public function test_cannot_add_product_with_insufficient_stock(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 100
        ]);
        
        $response->assertStatus(422)
            ->assertJsonStructure(['error']);
    }

    public function test_cannot_add_inactive_product(): void
    {
        $inactiveProduct = Product::create([
            'name' => 'Inactive Product',
            'sku' => 'INACTIVE-' . time(),
            'price' => 50.00,
            'active' => false
        ]);
        
        $response = $this->postJson('/api/cart', [
            'product_id' => $inactiveProduct->id,
            'quantity' => 1
        ]);
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_cart_calculates_totals_correctly(): void
    {
        $product2 = Product::firstOrCreate(
            ['sku' => 'CART-TEST-002'],
            [
                'name' => 'Second Cart Product',
                'price' => 50.00,
                'active' => true
            ]
        );
        
        Stock::firstOrCreate(
            ['product_id' => $product2->id],
            ['quantity' => 50, 'reserved' => 0]
        );
        
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2  // 2 x 100 = 200
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/cart', [
                'product_id' => $product2->id,
                'quantity' => 3  // 3 x 50 = 150
            ]);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $response->assertJsonPath('data.subtotal', 350)
            ->assertJsonPath('data.total', 350);
    }

    public function test_cart_persists_across_requests(): void
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $sessionCookie = $this->getSessionCookie($response);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.items');
    }

    public function test_can_add_product_with_variations(): void
    {
        $productWithVariations = Product::create([
            'name' => 'T-Shirt',
            'sku' => 'TSHIRT-' . time(),
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
            'quantity' => 1,
            'variations' => ['size' => 'M', 'color' => 'Blue']
        ]);
        
        $response->assertStatus(201);
    }
}