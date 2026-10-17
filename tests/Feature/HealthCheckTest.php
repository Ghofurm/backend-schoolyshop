<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
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
                    'services' => [
                        'database' => [
                            'status' => 'connected',
                        ],
                    ],
                ],
                'meta' => null,
                'errors' => null,
            ]);
    }

    public function test_health_check_returns_503_when_database_fails(): void
    {
        DB::shouldReceive('connection->getPdo')
            ->once()
            ->andThrow(new \PDOException('Connection refused'));

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'message' => 'System is unhealthy',
                'data' => [
                    'status' => 'unhealthy',
                    'services' => [
                        'database' => [
                            'status' => 'disconnected',
                        ],
                    ],
                ],
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
