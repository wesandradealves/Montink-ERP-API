<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Modules\Coupons\Models\Coupon;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class CompleteFlowTest extends TestCase
{
    private array $products = [];
    private User $user;
    private ?string $authToken = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        User::where('email', 'e2e@test.com')->delete();
        
        \DB::table('orders')->where('customer_email', 'customer@e2etest.com')->delete();
        
        $productIds = Product::where('sku', 'LIKE', 'NB-E2E-%')
            ->orWhere('sku', 'LIKE', 'MS-E2E-%')
            ->orWhere('sku', 'LIKE', 'KB-E2E-%')
            ->pluck('id');
        
        if ($productIds->isNotEmpty()) {
            \DB::table('order_items')->whereIn('product_id', $productIds)->delete();
            \DB::table('stock')->whereIn('product_id', $productIds)->delete();
            Product::whereIn('id', $productIds)->delete();
        }
        
        Coupon::where('code', 'E2E10OFF')->delete();
        
        $this->user = User::create([
            'name' => 'E2E Test User',
            'email' => 'e2e@test.com',
            'password' => Hash::make('password123')
        ]);
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'e2e@test.com',
            'password' => 'password123'
        ]);
        
        $this->authToken = $loginResponse->json('data.accessToken');
        
        $this->products[] = Product::create([
            'name' => 'Notebook',
            'sku' => 'NB-E2E-001',
            'price' => 2000.00,
            'active' => true
        ]);
        
        $this->products[] = Product::create([
            'name' => 'Mouse',
            'sku' => 'MS-E2E-001',
            'price' => 50.00,
            'active' => true
        ]);
        
        foreach ($this->products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => 100,
                'reserved' => 0
            ]);
        }
        
        Coupon::create([
            'code' => 'E2E10OFF',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth()
        ]);
    }

    public function test_complete_purchase_flow_with_authentication(): void
    {
        $response = $this->getJson('/api/products?only_active=true');
        $response->assertStatus(200);
        
        $response = $this->getJson("/api/products/{$this->products[0]->id}");
        $response->assertStatus(200);
        
        $cartResponse1 = $this->postJson('/api/cart', [
            'product_id' => $this->products[0]->id,
            'quantity' => 1
        ]);
        $cartResponse1->assertStatus(201);
        
        $sessionCookie = $this->getSessionCookie($cartResponse1);
        
        $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/cart', [
                'product_id' => $this->products[1]->id,
                'quantity' => 2
            ])
            ->assertStatus(201);
        
        $cartResponse = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $cartResponse->assertStatus(200)
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.subtotal', 2100); // 2000 + (50 * 2)
        
        
        $response = $this->getJson('/api/address/cep/01310100');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['cep', 'logradouro', 'bairro', 'localidade', 'uf']]);
        
        $orderResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'E2E Test Customer',
                'customer_email' => 'customer@e2etest.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderResponse->assertStatus(201)
            ->assertJsonPath('data.subtotal', 2100);
        
        $orderId = $orderResponse->json('data.id');
        $orderNumber = $orderResponse->json('data.order_number');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->authToken
        ])->getJson("/api/orders/{$orderId}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.order_number', $orderNumber);
        
        $response = $this->getJson('/api/orders?customer_email=customer@e2etest.com');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.items');
        
        $response = $this->postJson('/api/webhooks/order-status', [
            'order_id' => $orderId,
            'status' => 'processing'
        ]);
        $response->assertStatus(200);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        $response->assertJsonCount(0, 'data.items');
        
        $stock = Stock::where('product_id', $this->products[0]->id)->first();
        $this->assertEquals(1, $stock->reserved);
    }

    public function test_filter_products_by_price_in_complete_flow(): void
    {
        $response = $this->getJson('/api/products?max_price=100');
        $response->assertStatus(200);
        
        $cheapProducts = $response->json('data');
        $foundMouse = false;
        
        foreach ($cheapProducts as $product) {
            if ($product['sku'] === 'MS-E2E-001') {
                $foundMouse = true;
            }
            $this->assertLessThanOrEqual(100, floatval($product['price']));
        }
        
        $this->assertTrue($foundMouse, 'Mouse should be in cheap products');
        
        $response = $this->getJson('/api/products?min_price=1000');
        $response->assertStatus(200);
        
        $expensiveProducts = $response->json('data');
        $foundNotebook = false;
        
        foreach ($expensiveProducts as $product) {
            if ($product['sku'] === 'NB-E2E-001') {
                $foundNotebook = true;
            }
            $this->assertGreaterThanOrEqual(1000, floatval($product['price']));
        }
        
        $this->assertTrue($foundNotebook, 'Notebook should be in expensive products');
    }

    private function getSessionCookie($response): string
    {
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'session_id') {
                return $cookie->getValue();
            }
        }
        return '';
    }

    protected function tearDown(): void
    {
        \DB::table('order_items')->whereIn('product_id', collect($this->products)->pluck('id'))->delete();
        
        foreach ($this->products as $product) {
            Stock::where('product_id', $product->id)->delete();
            $product->delete();
        }
        
        User::where('email', 'e2e@test.com')->delete();
        Coupon::where('code', 'E2E10OFF')->delete();
        
        parent::tearDown();
    }
}