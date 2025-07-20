<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Modules\Email\Services\EmailService;
use App\Modules\Email\Services\EmailTemplateService;
use App\Modules\Email\DTOs\OrderConfirmationEmailDTO;
use App\Common\Enums\ResponseMessage;
use Illuminate\Support\Facades\Mail;
use Mockery;

class EmailServiceTest extends TestCase
{
    private EmailService $emailService;
    private $mockTemplateService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockTemplateService = Mockery::mock(EmailTemplateService::class);
        $this->emailService = new EmailService($this->mockTemplateService);
        
        Mail::fake();
    }

    public function test_can_send_order_confirmation_email(): void
    {
        $dto = new OrderConfirmationEmailDTO(
            orderNumber: 'ORD-TEST-001',
            customerName: 'Test Customer',
            customerEmail: 'customer@example.com',
            customerPhone: '(11) 99999-9999',
            customerCpf: '123.456.789-00',
            customerCep: '01310-100',
            customerAddress: 'Av. Paulista, 1000',
            customerComplement: 'Apto 101',
            customerNeighborhood: 'Bela Vista',
            customerCity: 'São Paulo',
            customerState: 'SP',
            items: [
                [
                    'name' => 'Test Product',
                    'quantity' => 2,
                    'price' => 'R$ 50,00',
                    'subtotal' => 'R$ 100,00'
                ]
            ],
            subtotal: 100.00,
            discount: 10.00,
            couponCode: 'DESC10',
            shippingCost: 15.00,
            total: 105.00,
            createdAt: now()
        );
        
        $this->mockTemplateService->shouldReceive('send')
            ->once()
            ->with('order_confirmation', 'customer@example.com', Mockery::type('array'), 'Test Customer')
            ->andReturn(true);
        
        $result = $this->emailService->sendOrderConfirmationEmail($dto);
        
        $this->assertTrue($result);
    }

    public function test_email_service_handles_send_failure(): void
    {
        $dto = new OrderConfirmationEmailDTO(
            orderNumber: 'ORD-TEST-002',
            customerName: 'Test Customer',
            customerEmail: 'invalid-email',
            customerPhone: '(11) 99999-9999',
            customerCpf: '123.456.789-00',
            customerCep: '01310-100',
            customerAddress: 'Av. Paulista, 1000',
            customerComplement: null,
            customerNeighborhood: 'Bela Vista',
            customerCity: 'São Paulo',
            customerState: 'SP',
            items: [],
            subtotal: 100.00,
            discount: 0,
            couponCode: null,
            shippingCost: 0,
            total: 100.00,
            createdAt: now()
        );
        
        $this->mockTemplateService->shouldReceive('send')
            ->once()
            ->andReturn(false);
        
        $result = $this->emailService->sendOrderConfirmationEmail($dto);
        
        $this->assertFalse($result);
    }

    public function test_email_data_formatting(): void
    {
        $dto = new OrderConfirmationEmailDTO(
            orderNumber: 'ORD-12345',
            customerName: 'John Doe',
            customerEmail: 'john@example.com',
            customerPhone: '(11) 98765-4321',
            customerCpf: '987.654.321-00',
            customerCep: '01310100',
            customerAddress: 'Rua Teste, 123',
            customerComplement: 'Sala 10',
            customerNeighborhood: 'Centro',
            customerCity: 'São Paulo',
            customerState: 'SP',
            items: [
                [
                    'name' => 'Produto A',
                    'quantity' => 1,
                    'price' => 'R$ 50,00',
                    'subtotal' => 'R$ 50,00'
                ],
                [
                    'name' => 'Produto B',
                    'quantity' => 2,
                    'price' => 'R$ 25,00',
                    'subtotal' => 'R$ 50,00'
                ]
            ],
            subtotal: 100.00,
            discount: 10.00,
            couponCode: 'PROMO10',
            shippingCost: 20.00,
            total: 110.00,
            createdAt: now()
        );
        
        $expectedData = null;
        
        $this->mockTemplateService->shouldReceive('send')
            ->once()
            ->with('order_confirmation', 'john@example.com', Mockery::on(function($data) use (&$expectedData) {
                $expectedData = $data;
                return true;
            }), 'John Doe')
            ->andReturn(true);
        
        $this->emailService->sendOrderConfirmationEmail($dto);
        
        $this->assertEquals('ORD-12345', $expectedData['orderNumber']);
        $this->assertEquals('John Doe', $expectedData['customerName']);
        $this->assertEquals('R$ 100,00', $expectedData['subtotal']);
        $this->assertEquals('R$ 10,00', $expectedData['discount']);
        $this->assertEquals('R$ 20,00', $expectedData['shippingCost']);
        $this->assertEquals('R$ 110,00', $expectedData['total']);
        $this->assertEquals('PROMO10', $expectedData['couponCode']);
        $this->assertStringContainsString('Rua Teste, 123, Sala 10', $expectedData['deliveryAddress']);
        $this->assertStringContainsString('CEP: 01310-100', $expectedData['deliveryAddress']);
    }

    public function test_email_without_optional_fields(): void
    {
        $dto = new OrderConfirmationEmailDTO(
            orderNumber: 'ORD-12345',
            customerName: 'John Doe',
            customerEmail: 'john@example.com',
            customerPhone: '(11) 99999-9999',
            customerCpf: '123.456.789-00',
            customerCep: '01310100',
            customerAddress: 'Rua Teste, 123',
            customerComplement: null,
            customerNeighborhood: 'Centro',
            customerCity: 'São Paulo',
            customerState: 'SP',
            items: [],
            subtotal: 100.00,
            discount: 0,
            couponCode: null,
            shippingCost: 0,
            total: 100.00,
            createdAt: now()
        );
        
        $expectedData = null;
        
        $this->mockTemplateService->shouldReceive('send')
            ->once()
            ->with('order_confirmation', 'john@example.com', Mockery::on(function($data) use (&$expectedData) {
                $expectedData = $data;
                return true;
            }), 'John Doe')
            ->andReturn(true);
        
        $result = $this->emailService->sendOrderConfirmationEmail($dto);
        
        $this->assertTrue($result);
        $this->assertNull($expectedData['discount']);
        $this->assertNull($expectedData['couponCode']);
        $this->assertStringNotContainsString(', -', $expectedData['deliveryAddress']);
    }

    public function test_email_template_service_integration(): void
    {
        $this->assertTrue(class_exists(EmailTemplateService::class));
        
        $this->assertIsArray(config('mail'));
    }

    public function test_email_service_respects_environment_config(): void
    {
        $this->assertEquals('array', config('mail.default'));
    }

    public function test_multiple_emails_can_be_sent(): void
    {
        $this->mockTemplateService->shouldReceive('send')
            ->times(3)
            ->andReturn(true);
        
        for ($i = 1; $i <= 3; $i++) {
            $dto = new OrderConfirmationEmailDTO(
                orderNumber: "ORD-TEST-00{$i}",
                customerName: "Customer {$i}",
                customerEmail: "customer{$i}@example.com",
                customerPhone: '(11) 99999-9999',
                customerCpf: '123.456.789-00',
                customerCep: '01310-100',
                customerAddress: 'Test Address',
                customerComplement: null,
                customerNeighborhood: 'Test',
                customerCity: 'São Paulo',
                customerState: 'SP',
                items: [],
                subtotal: 100.00 * $i,
                discount: 0,
                couponCode: null,
                shippingCost: 10.00,
                total: (100.00 * $i) + 10.00,
                createdAt: now()
            );
            
            $result = $this->emailService->sendOrderConfirmationEmail($dto);
            $this->assertTrue($result);
        }
    }

    public function test_email_service_returns_false_on_failure(): void
    {
        $dto = new OrderConfirmationEmailDTO(
            orderNumber: 'ORD-ERROR',
            customerName: 'Test',
            customerEmail: 'test@example.com',
            customerPhone: '(11) 99999-9999',
            customerCpf: '123.456.789-00',
            customerCep: '01310-100',
            customerAddress: 'Test Address',
            customerComplement: null,
            customerNeighborhood: 'Test',
            customerCity: 'São Paulo',
            customerState: 'SP',
            items: [],
            subtotal: 100.00,
            discount: 0,
            couponCode: null,
            shippingCost: 0,
            total: 100.00,
            createdAt: now()
        );
        
        $this->mockTemplateService->shouldReceive('send')
            ->once()
            ->andReturn(false);
        
        $result = $this->emailService->sendOrderConfirmationEmail($dto);
        
        $this->assertFalse($result);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}