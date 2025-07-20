<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Modules\Orders\Models\Order;
use App\Modules\Coupons\Models\Coupon;
use App\Common\Enums\ResponseMessage;

class OrdersTest extends TestCase
{
    private Product $product;
    private string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->product = Product::firstOrCreate(
            ['sku' => 'ORDER-TEST-001'],
            [
                'name' => 'Order Test Product',
                'price' => 100.00,
                'active' => true
            ]
        );

        $stock = Stock::where('product_id', $this->product->id)->first();
        if ($stock) {
            $stock->update([
                'quantity' => 100,
                'reserved' => 0
            ]);
        } else {
            Stock::create([
                'product_id' => $this->product->id,
                'quantity' => 100,
                'reserved' => 0
            ]);
        }
    }

    private function addProductToCart(): string
    {
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $response->assertStatus(201);
        
        $cookies = $response->headers->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'session_id') {
                return $cookie->getValue();
            }
        }
        
        return '';
    }

    public function test_can_create_order_from_cart(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $orderData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_cpf' => '123.456.789-00',
            'customer_phone' => '(11) 98765-4321',
            'customer_cep' => '01310-100',
            'customer_address' => 'Av. Paulista, 1000',
            'customer_neighborhood' => 'Bela Vista',
            'customer_city' => 'São Paulo',
            'customer_state' => 'SP'
        ];
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', $orderData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'orderNumber',
                    'status',
                    'subtotal',
                    'discount',
                    'total',
                    'customerName',
                    'customerEmail',
                    'items'
                ],
                'message'
            ])
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total', 200)
            ->assertJsonPath('message', ResponseMessage::ORDER_CREATED->get());
        
        $cartResponse = $this->withCookie('session_id', $sessionCookie)
            ->getJson('/api/cart');
        
        $cartResponse->assertJsonCount(0, 'data.items');
    }

    public function test_cannot_create_order_with_empty_cart(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_cep', 'customer_address', 'customer_neighborhood', 'customer_city', 'customer_state']);
    }

    public function test_order_validation(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'customer_email']);
    }

    public function test_can_list_orders(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'List Test Customer',
                'customer_email' => 'list@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $response = $this->getJson('/api/orders');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'orderNumber',
                            'status',
                            'total',
                            'customerName',
                            'customerEmail',
                            'createdAt'
                        ]
                    ],
                    'total'
                ],
                'message'
            ]);
    }

    public function test_can_filter_orders_by_status(): void
    {
        $response = $this->getJson('/api/orders?status=pending');
        
        $response->assertStatus(200);
        
        $orders = $response->json('data.items');
        foreach ($orders as $order) {
            $this->assertEquals('pending', $order['status']);
        }
    }

    public function test_can_filter_orders_by_customer_email(): void
    {
        $email = 'specific@example.com';
        $response = $this->getJson("/api/orders?customer_email={$email}");
        
        $response->assertStatus(200);
        
        $orders = $response->json('data.items');
        foreach ($orders as $order) {
            $this->assertEquals($email, $order['customerEmail']);
        }
    }

    public function test_can_get_order_by_id(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderId = $createResponse->json('data.id');
        
        $response = $this->getJson("/api/orders/{$orderId}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.original.data.id', $orderId)
            ->assertJsonPath('data.original.message', ResponseMessage::ORDER_FOUND->get());
    }

    public function test_can_get_order_by_number(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderNumber = $createResponse->json('data.orderNumber');
        
        $response = $this->getJson("/api/orders/number/{$orderNumber}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.original.data.orderNumber', $orderNumber);
    }

    public function test_returns_404_for_non_existent_order(): void
    {
        $response = $this->getJson('/api/orders/999999');
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_can_update_order_status(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderId = $createResponse->json('data.id');
        
        $response = $this->patchJson("/api/orders/{$orderId}/status", [
            'status' => 'processing'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.original.data.status', 'processing')
            ->assertJsonPath('data.original.message', ResponseMessage::ORDER_STATUS_UPDATED->get());
    }

    public function test_cannot_update_to_invalid_status(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderId = $createResponse->json('data.id');
        
        $response = $this->patchJson("/api/orders/{$orderId}/status", [
            'status' => 'invalid_status'
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_cancel_order(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderId = $createResponse->json('data.id');
        
        $response = $this->deleteJson("/api/orders/{$orderId}");
        
        $response->assertStatus(200)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_cannot_cancel_shipped_order(): void
    {
        $sessionCookie = $this->addProductToCart();
        
        $createResponse = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $orderId = $createResponse->json('data.id');
        
        $this->patchJson("/api/orders/{$orderId}/status", [
            'status' => 'shipped'
        ]);
        
        $response = $this->deleteJson("/api/orders/{$orderId}");
        
        $response->assertStatus(500)
            ->assertJsonStructure(['error']);
    }

    public function test_order_with_coupon_applies_discount(): void
    {
        $coupon = Coupon::firstOrCreate(
            ['code' => 'TEST10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonth()
            ]
        );
        
        $sessionCookie = $this->addProductToCart();
        
        $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/cart/coupon', [
                'code' => 'TEST10'
            ]);
        
        $response = $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP',
                'coupon_code' => 'TEST10'
            ]);
        
        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', 200);
    }

    public function test_order_reserves_stock(): void
    {
        $stock = Stock::where('product_id', $this->product->id)->first();
        $initialReserved = $stock->reserved;
        
        $sessionCookie = $this->addProductToCart();
        
        $this->withCookie('session_id', $sessionCookie)
            ->postJson('/api/orders', [
                'customer_name' => 'Test Customer',
                'customer_email' => 'test@example.com',
                'customer_cpf' => '123.456.789-00',
                'customer_phone' => '(11) 98765-4321',
                'customer_cep' => '01310-100',
                'customer_address' => 'Av. Paulista, 1000',
                'customer_neighborhood' => 'Bela Vista',
                'customer_city' => 'São Paulo',
                'customer_state' => 'SP'
            ]);
        
        $stock->refresh();
        $this->assertEquals(2, $stock->reserved);
    }
}