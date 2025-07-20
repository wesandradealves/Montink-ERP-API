<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Coupons\Models\Coupon;
use App\Common\Enums\ResponseMessage;

class CouponsTest extends TestCase
{
    private Coupon $testCoupon;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->testCoupon = Coupon::updateOrCreate(
            ['code' => 'TESTCOUPON10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonth(),
                'minimum_value' => 50,
                'usage_limit' => 100,
                'used_count' => 0
            ]
        );
    }

    public function test_can_list_coupons(): void
    {
        $response = $this->getJson('/api/coupons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'code',
                            'type',
                            'value',
                            'active',
                            'valid_from',
                            'valid_until',
                            'minimum_value',
                            'usage_limit',
                            'used_count'
                        ]
                    ],
                    'total'
                ],
                'message'
            ]);
    }

    public function test_can_filter_active_coupons(): void
    {
        $response = $this->getJson('/api/coupons?active=true');
        
        $response->assertStatus(200);
        
        $coupons = $response->json('data.items');
        foreach ($coupons as $coupon) {
            $this->assertTrue($coupon['active']);
        }
    }

    public function test_can_search_coupons_by_code(): void
    {
        $response = $this->getJson('/api/coupons?search=TEST');
        
        $response->assertStatus(200);
        
        $coupons = $response->json('data.items');
        if (count($coupons) > 0) {
            foreach ($coupons as $coupon) {
                $this->assertStringContainsStringIgnoringCase('test', $coupon['code']);
            }
        }
    }

    public function test_can_get_coupon_by_id(): void
    {
        $response = $this->getJson("/api/coupons/{$this->testCoupon->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->testCoupon->id)
            ->assertJsonPath('data.code', $this->testCoupon->code)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_can_get_coupon_by_code(): void
    {
        $response = $this->getJson("/api/coupons/code/{$this->testCoupon->code}");
        
        $response->assertStatus(200)
            ->assertJsonPath('data.code', $this->testCoupon->code)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_returns_404_for_non_existent_coupon(): void
    {
        $response = $this->getJson('/api/coupons/999999');
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_can_create_percentage_coupon(): void
    {
        $code = 'NEWTEST20-' . time();
        $couponData = [
            'code' => $code,
            'type' => 'percentage',
            'value' => 20,
            'active' => true,
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addMonth()->format('Y-m-d'),
            'minimum_value' => 100,
            'usage_limit' => 50
        ];
        
        $response = $this->postJson('/api/coupons', $couponData);
        
        $response->assertStatus(201)
            ->assertJsonPath('data.code', $code)
            ->assertJsonPath('data.type', 'percentage')
            ->assertJsonPath('data.value', 20)
            ->assertJsonPath('message', ResponseMessage::DEFAULT_CREATED->get());
    }

    public function test_can_create_fixed_value_coupon(): void
    {
        $code = 'FIXED50-' . time();
        $couponData = [
            'code' => $code,
            'type' => 'fixed',
            'value' => 50,
            'active' => true,
            'valid_from' => now()->format('Y-m-d'),
            'valid_until' => now()->addMonth()->format('Y-m-d')
        ];
        
        $response = $this->postJson('/api/coupons', $couponData);
        
        $response->assertStatus(201)
            ->assertJsonPath('data.code', $code)
            ->assertJsonPath('data.type', 'fixed')
            ->assertJsonPath('data.value', 50);
    }

    public function test_cannot_create_coupon_with_duplicate_code(): void
    {
        $couponData = [
            'code' => $this->testCoupon->code,
            'type' => 'percentage',
            'value' => 15
        ];
        
        $response = $this->postJson('/api/coupons', $couponData);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_create_coupon_validation(): void
    {
        $response = $this->postJson('/api/coupons', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'type', 'value']);
    }

    public function test_can_update_coupon(): void
    {
        $updateData = [
            'value' => 15,
            'active' => false
        ];
        
        $response = $this->patchJson("/api/coupons/{$this->testCoupon->id}", $updateData);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.value', 15)
            ->assertJsonPath('data.active', false)
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_can_delete_coupon(): void
    {
        $code = 'DELETE-TEST-' . time();
        $coupon = Coupon::firstOrCreate(
            ['code' => $code],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true
            ]
        );
        
        $response = $this->deleteJson("/api/coupons/{$coupon->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'message']);
    }

    public function test_can_validate_coupon(): void
    {
        $simpleCode = 'SIMPLE-' . time();
        $simpleCoupon = Coupon::firstOrCreate(
            ['code' => $simpleCode],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $simpleCode,
            'value' => 100
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'valid',
                    'discount',
                    'coupon',
                    'final_value'
                ]
            ])
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.discount', 10);
    }

    public function test_validate_coupon_with_minimum_amount(): void
    {
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $this->testCoupon->code,
            'value' => 30  // Abaixo do mínimo de 50
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::COUPON_MINIMUM_NOT_MET->get()]);
    }

    public function test_validate_expired_coupon(): void
    {
        $code = 'EXPIRED-' . time();
        $expiredCoupon = Coupon::firstOrCreate(
            ['code' => $code],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'valid_from' => now()->subMonth(),
                'valid_until' => now()->subDay()
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $code,
            'value' => 100
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::COUPON_EXPIRED->get()]);
    }

    public function test_validate_inactive_coupon(): void
    {
        $code = 'INACTIVE-' . time();
        $inactiveCoupon = Coupon::firstOrCreate(
            ['code' => $code],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => false
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $code,
            'value' => 100
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::COUPON_INACTIVE->get()]);
    }

    public function test_validate_usage_limit_reached(): void
    {
        $code = 'LIMITED-' . time();
        $limitedCoupon = Coupon::firstOrCreate(
            ['code' => $code],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'usage_limit' => 1,
                'used_count' => 1
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $code,
            'value' => 100
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::COUPON_USAGE_LIMIT_REACHED->get()]);
    }

    public function test_validate_non_existent_coupon(): void
    {
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'NONEXISTENT',
            'value' => 100
        ]);
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_fixed_coupon_discount_calculation(): void
    {
        $code = 'FIXED25-' . time();
        $fixedCoupon = Coupon::firstOrCreate(
            ['code' => $code],
            [
                'type' => 'fixed',
                'value' => 25,
                'active' => true
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $code,
            'value' => 100
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.discount', 25);
    }

    public function test_percentage_coupon_discount_calculation(): void
    {
        $percCode = 'PERC-' . time();
        $percCoupon = Coupon::firstOrCreate(
            ['code' => $percCode],
            [
                'type' => 'percentage',
                'value' => 10,
                'active' => true
            ]
        );
        
        $response = $this->postJson('/api/coupons/validate', [
            'code' => $percCode,
            'value' => 150
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.discount', 15); // 10% de 150
    }
}