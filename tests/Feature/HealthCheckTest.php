<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_returns_success_envelope(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'System is healthy',
                'data' => [
                    'status' => 'healthy',
                ],
                'meta' => null,
                'errors' => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'status',
                    'timestamp',
                    'services' => [
                        'database' => [
                            'status',
                            'error',
                        ],
                    ],
                ],
                'meta',
                'errors',
            ]);
    }

    public function test_unknown_api_route_returns_json_404(): void
    {
        $response = $this->get('/api/v1/non-existent-route-endpoint');

        $response->assertStatus(404)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ]);
    }
}
