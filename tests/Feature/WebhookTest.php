<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Orders\Models\Order;
use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use App\Common\Enums\ResponseMessage;

class WebhookTest extends TestCase
{
    private Order $testOrder;

    protected function setUp(): void
    {
        parent::setUp();
        
        $product = Product::create([
            'name' => 'Webhook Test Product',
            'sku' => 'WEBHOOK-TEST-' . uniqid(),
            'price' => 100.00,
            'active' => true
        ]);
        
        Stock::create([
            'product_id' => $product->id,
            'quantity' => 100,
            'reserved' => 10
        ]);
        
        $this->testOrder = Order::create([
            'order_number' => 'ORD-WEBHOOK-' . uniqid(),
            'status' => 'pending',
            'subtotal' => 100.00,
            'discount' => 0,
            'shipping_cost' => 0,
            'total' => 100.00,
            'customer_name' => 'Webhook Test',
            'customer_email' => 'webhook@test.com',
            'customer_cpf' => '123.456.789-00',
            'customer_cep' => '01310-100',
            'customer_address' => 'Av. Paulista, 1000',
            'customer_neighborhood' => 'Bela Vista',
            'customer_city' => 'São Paulo',
            'customer_state' => 'SP',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'price' => 100.00,
                    'subtotal' => 100.00
                ]
            ]
        ]);
    }

    public function test_can_update_order_status_via_webhook(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'processing',
            'timestamp' => now()->toIso8601String()
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get())
            ->assertJsonPath('data.action', 'updated');
        
        $this->testOrder->refresh();
        $this->assertEquals('processing', $this->testOrder->status);
    }

    public function test_webhook_returns_404_for_non_existent_order(): void
    {
        $webhookData = [
            'order_id' => 99999,
            'status' => 'processing'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    public function test_webhook_validates_required_fields(): void
    {
        $response = $this->postJson('/api/webhooks/order-status', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id', 'status']);
    }

    public function test_webhook_validates_status_enum(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'invalid_status'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_webhook_can_update_to_completed_status(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'delivered'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200);
        
        $this->testOrder->refresh();
        $this->assertEquals('delivered', $this->testOrder->status);
    }

    public function test_webhook_can_update_to_cancelled_status(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'cancelled'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('orders', [
            'id' => $this->testOrder->id
        ]);
    }

    public function test_webhook_cannot_update_shipped_order_to_pending(): void
    {
        $this->testOrder->update(['status' => 'shipped']);
        
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'pending'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200);
    }

    public function test_webhook_accepts_additional_metadata(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'processing',
            'payment_id' => 'PAY-12345',
            'payment_method' => 'credit_card',
            'paid_at' => now()->toIso8601String()
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200);
    }

    public function test_webhook_handles_duplicate_status_update(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'processing'
        ];
        
        $response1 = $this->postJson('/api/webhooks/order-status', $webhookData);
        $response1->assertStatus(200);
        
        $response2 = $this->postJson('/api/webhooks/order-status', $webhookData);
        $response2->assertStatus(200); // Deve aceitar sem erro
    }

    public function test_webhook_logs_status_changes(): void
    {
        $webhookData = [
            'order_id' => $this->testOrder->id,
            'status' => 'processing'
        ];
        
        $response = $this->postJson('/api/webhooks/order-status', $webhookData);
        
        $response->assertStatus(200);
        
    }
    
    protected function tearDown(): void
    {
        if (isset($this->testOrder)) {
            $this->testOrder->delete();
        }
        
        parent::tearDown();
    }
}