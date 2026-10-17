<?php

namespace Tests\Unit;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_paginated_response_only_includes_resource_fields(): void
    {
        $user = new User([
            'id_user' => 1,
            'nama' => 'Test User',
            'email' => 'test@schooly.test',
            'no_hp' => '081234567890',
            'password' => 'secret_hash',
            'remember_token' => 'secret_token',
        ]);
        $user->id_user = 1;

        $paginator = new LengthAwarePaginator(
            items: collect([$user]),
            total: 1,
            perPage: 15,
            currentPage: 1
        );

        $response = ApiResponse::paginated($paginator, UserResource::class);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $data['data']);

        $item = $data['data'][0];
        $this->assertEquals(1, $item['id_user']);
        $this->assertEquals('Test User', $item['nama']);
        $this->assertEquals('test@schooly.test', $item['email']);

        // Pastikan atribut sensitif yang tidak diexpose oleh UserResource TIDAK ADA
        $this->assertArrayNotHasKey('password', $item);
        $this->assertArrayNotHasKey('remember_token', $item);
    }
}
