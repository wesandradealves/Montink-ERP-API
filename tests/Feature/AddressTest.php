<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Common\Enums\ResponseMessage;

class AddressTest extends TestCase
{
    public function test_can_get_address_by_valid_cep(): void
    {
        $response = $this->getJson('/api/address/cep/01310100');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'cep',
                    'logradouro',
                    'bairro',
                    'localidade',
                    'uf'
                ],
                'message'
            ])
            ->assertJsonPath('data.localidade', 'São Paulo')
            ->assertJsonPath('data.uf', 'SP')
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_returns_404_for_non_existent_cep(): void
    {
        $response = $this->getJson('/api/address/cep/99999999');
        
        $response->assertStatus(404)
            ->assertJsonStructure(['error']);
    }

    public function test_returns_422_for_invalid_cep_format(): void
    {
        $response = $this->getJson('/api/address/cep/1234');
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::ADDRESS_CEP_INVALID_FORMAT->get()]);
    }

    public function test_cep_must_contain_only_numbers(): void
    {
        $response = $this->getJson('/api/address/cep/0131010A');
        
        $response->assertStatus(422)
            ->assertJson(['error' => ResponseMessage::ADDRESS_CEP_INVALID_FORMAT->get()]);
    }

    public function test_can_validate_existing_cep(): void
    {
        $response = $this->postJson('/api/address/validate-cep', [
            'cep' => '01310100'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'valid',
                    'cep'
                ],
                'message'
            ])
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.cep', '01310100')
            ->assertJsonPath('message', ResponseMessage::OPERATION_SUCCESS->get());
    }

    public function test_validate_non_existent_cep(): void
    {
        $response = $this->postJson('/api/address/validate-cep', [
            'cep' => '99999999'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.valid', false)
            ->assertJsonPath('data.cep', '99999999');
    }

    public function test_validate_cep_requires_cep_field(): void
    {
        $response = $this->postJson('/api/address/validate-cep', []);
        
        $response->assertStatus(500)
            ->assertJsonStructure(['error', 'type']);
    }

    public function test_validate_cep_format(): void
    {
        $response = $this->postJson('/api/address/validate-cep', [
            'cep' => '1234'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.valid', false);
    }

    public function test_cep_endpoint_accepts_dashes(): void
    {
        $response = $this->getJson('/api/address/cep/01310-100');
        
        $response->assertStatus(200)
            ->assertJsonPath('data.localidade', 'São Paulo');
    }

    public function test_cep_validation_handles_formatted_cep(): void
    {
        $response = $this->postJson('/api/address/validate-cep', [
            'cep' => '01310-100'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.valid', true);
    }

    public function test_address_response_includes_all_fields(): void
    {
        $response = $this->getJson('/api/address/cep/01310100');
        
        $response->assertStatus(200);
        
        $address = $response->json('data');
        
        $this->assertArrayHasKey('cep', $address);
        $this->assertArrayHasKey('logradouro', $address);
        $this->assertArrayHasKey('bairro', $address);
        $this->assertArrayHasKey('localidade', $address);
        $this->assertArrayHasKey('uf', $address);
        
        $this->assertNotNull($address['cep']);
        $this->assertNotNull($address['localidade']);
        $this->assertNotNull($address['uf']);
    }

    public function test_cep_search_is_case_insensitive(): void
    {
        $response1 = $this->getJson('/api/address/cep/01310100');
        $response2 = $this->getJson('/api/address/cep/01310100');
        
        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        $this->assertEquals(
            $response1->json('data.street'),
            $response2->json('data.street')
        );
    }
}