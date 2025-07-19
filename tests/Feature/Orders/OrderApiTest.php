<?php

namespace Tests\Feature\Orders;

use Tests\TestCase;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Modules\Orders\Models\Order;
use App\Modules\Coupons\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100.00,
            'active' => true
        ]);

        Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 100,
            'reserved' => 0
        ]);
    }

    public function test_can_create_order_from_cart(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $orderData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_cpf' => '123.456.789-00',
            'customer_phone' => '(11) 98765-4321',
            'shipping_cep' => '01310-100',
            'shipping_address' => 'Av. Paulista, 1000',
            'shipping_neighborhood' => 'Bela Vista',
            'shipping_city' => 'São Paulo',
            'shipping_state' => 'SP'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_number',
                    'status',
                    'subtotal',
                    'discount',
                    'total',
                    'customer' => [
                        'name',
                        'email',
                        'cpf',
                        'phone'
                    ],
                    'shipping' => [
                        'cep',
                        'address',
                        'neighborhood',
                        'city',
                        'state'
                    ],
                    'items' => [
                        '*' => [
                            'id',
                            'product_id',
                            'product_name',
                            'quantity',
                            'price',
                            'subtotal'
                        ]
                    ]
                ],
                'message'
            ])
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total', 200.00)
            ->assertJsonPath('message', 'Pedido criado com sucesso');

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'total' => 200.00
        ]);

        $cartResponse = $this->getJson('/api/cart');
        $cartResponse->assertJsonCount(0, 'data.items');
    }

    public function test_cannot_create_order_with_empty_cart(): void
    {
        $orderData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Carrinho vazio. Adicione produtos antes de finalizar o pedido.'
            ]);
    }

    public function test_can_list_orders_with_filters(): void
    {
        Order::factory()->count(5)->create();
        
        Order::factory()->create([
            'customer_email' => 'specific@example.com',
            'status' => 'completed'
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'order_number',
                        'status',
                        'total',
                        'customer',
                        'created_at'
                    ]
                ]
            ])
            ->assertJsonCount(6, 'data');

        $filteredResponse = $this->getJson('/api/orders?customer_email=specific@example.com');
        $filteredResponse->assertJsonCount(1, 'data');

        $statusResponse = $this->getJson('/api/orders?status=completed');
        $statusResponse->assertJsonPath('data.0.status', 'completed');
    }

    public function test_can_get_order_by_id(): void
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.order_number', $order->order_number)
            ->assertJsonPath('message', 'Pedido encontrado com sucesso');
    }

    public function test_can_get_order_by_number(): void
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/number/{$order->order_number}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.order_number', $order->order_number);
    }

    public function test_returns_404_for_nonexistent_order(): void
    {
        $response = $this->getJson('/api/orders/99999');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Pedido não encontrado'
            ]);
    }

    public function test_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'paid'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('message', 'Status do pedido atualizado com sucesso');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid'
        ]);
    }

    public function test_cannot_update_to_invalid_status(): void
    {
        $order = Order::factory()->create();

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_cancel_order(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled')
            ->assertJsonPath('message', 'Pedido cancelado com sucesso');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_cannot_cancel_shipped_order(): void
    {
        $order = Order::factory()->create(['status' => 'shipped']);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Pedido não pode ser cancelado no status atual: shipped'
            ]);
    }

    public function test_order_with_coupon_applies_discount(): void
    {
        $coupon = Coupon::create([
            'code' => 'DISCOUNT10',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth()
        ]);

        $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $this->postJson('/api/cart/coupon', [
            'code' => 'DISCOUNT10'
        ]);

        $orderData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', 200.00)
            ->assertJsonPath('data.discount', 20.00)
            ->assertJsonPath('data.total', 180.00)
            ->assertJsonPath('data.coupon.code', 'DISCOUNT10');
    }

    public function test_order_reserves_stock(): void
    {
        $initialStock = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(0, $initialStock->reserved);

        $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);

        $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ]);

        $updatedStock = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(5, $updatedStock->reserved);
        $this->assertEquals(95, $updatedStock->quantity - $updatedStock->reserved);
    }

    public function test_cancelled_order_releases_stock(): void
    {
        $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ]);

        $orderId = $orderResponse->json('data.id');

        $stockBefore = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(5, $stockBefore->reserved);

        $this->deleteJson("/api/orders/{$orderId}");

        $stockAfter = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(0, $stockAfter->reserved);
    }
}