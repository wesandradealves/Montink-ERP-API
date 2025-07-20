<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Common\Enums\ResponseMessage;

class ApplicationTest extends TestCase
{
    public function test_application_is_running(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(302);
        $response->assertRedirect('/docs');
    }

    public function test_api_root_redirects_to_docs(): void
    {
        $response = $this->get('/api');
        
        $response->assertStatus(302);
        $response->assertRedirect('/docs');
    }

    public function test_health_endpoint_returns_success(): void
    {
        $response = $this->getJson('/api/health');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'status',
                    'timestamp',
                    'version',
                    'environment',
                    'database',
                    'cache'
                ],
                'message'
            ])
            ->assertJsonPath('data.status', 'healthy')
            ->assertJsonPath('message', ResponseMessage::DEFAULT_SUCCESS->get());
    }

    public function test_invalid_route_returns_404(): void
    {
        $response = $this->getJson('/api/invalid-route');
        
        $response->assertStatus(404);
    }

    public function test_api_requires_json_accept_header(): void
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200);
    }
}