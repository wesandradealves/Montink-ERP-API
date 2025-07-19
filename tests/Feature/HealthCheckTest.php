<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_success(): void
    {
        $response = $this->get('/api/health');

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
            ->assertJsonPath('data.environment', 'testing')
            ->assertJsonPath('message', 'Success');
    }

    public function test_root_redirects_to_docs(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/docs');
    }

    public function test_api_root_redirects_to_docs(): void
    {
        $response = $this->get('/api');

        $response->assertRedirect('/docs');
    }

    public function test_invalid_endpoint_returns_404(): void
    {
        $response = $this->withHeaders($this->defaultHeaders())
            ->get('/api/invalid-endpoint');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'The route api/invalid-endpoint could not be found.'
            ]);
    }
}