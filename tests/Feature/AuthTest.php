<?php

namespace Tests\Feature;

use App\Enums\StatusAkun;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Pembeli',
            'email' => 'Budi@Example.Com',
            'no_hp' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registrasi berhasil.',
                'data' => [
                    'nama' => 'Budi Pembeli',
                    'email' => 'budi@example.com', // normalized lowercase
                    'no_hp' => '081234567890',
                ],
            ]);

        // Pastikan tidak mengembalikan token
        $this->assertArrayNotHasKey('token', $response->json('data'));

        $this->assertDatabaseHas('user', [
            'email' => 'budi@example.com',
            'status_akun' => StatusAkun::AKTIF->value,
        ]);
    }

    public function test_register_fails_on_duplicate_email(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Lain',
            'email' => 'budi@example.com',
            'no_hp' => '081234567899',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_register_fails_on_weak_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Pembeli',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_login_user_success(): void
    {
        $user = User::factory()->create([
            'email' => 'user@schooly.test',
            'password' => 'password123',
            'status_akun' => StatusAkun::AKTIF,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'USER@schooly.test', // Case-insensitive
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id_user', 'nama', 'email', 'no_hp'],
                ],
            ]);

        $this->assertEquals('Bearer', $response->json('data.token_type'));
    }

    public function test_login_user_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@schooly.test',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@schooly.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email atau kata sandi salah.',
            ]);
    }

    public function test_login_user_fails_when_account_inactive(): void
    {
        User::factory()->create([
            'email' => 'inactive@schooly.test',
            'password' => 'password123',
            'status_akun' => StatusAkun::NONAKTIF,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@schooly.test',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Akun tidak aktif.',
            ]);
    }

    public function test_login_throttling(): void
    {
        $email = 'throttle@schooly.test';

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $email,
                'password' => 'wrongpass',
            ])->assertStatus(401);
        }

        // Percobaan ke-6 harus di-throttle 429
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429);
    }

    public function test_user_logout_revokes_token(): void
    {
        $user = User::factory()->create(['status_akun' => StatusAkun::AKTIF]);
        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil.']);

        // Token lama sekarang harus 401
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/profile')
            ->assertStatus(401);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
        $this->getJson('/api/v1/admin/auth/me')->assertStatus(401);
    }

    public function test_role_user_rejects_admin_token_with_403(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);
        $adminToken = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->getJson('/api/v1/profile')
            ->assertStatus(403)
            ->assertJson(['message' => 'Akses ditolak.']);
    }

    public function test_role_admin_rejects_user_token_with_403(): void
    {
        $user = User::factory()->create(['status_akun' => StatusAkun::AKTIF]);
        $userToken = $user->createToken('user-token', ['user'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $userToken)
            ->getJson('/api/v1/admin/auth/me')
            ->assertStatus(403)
            ->assertJson(['message' => 'Akses ditolak.']);
    }

    public function test_get_and_update_profile(): void
    {
        $user = User::factory()->create([
            'nama' => 'Nama Awal',
            'email' => 'profil@schooly.test',
            'no_hp' => '0811111111',
            'status_akun' => StatusAkun::AKTIF,
        ]);
        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        // GET Profile
        $getResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/profile');

        $getResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nama' => 'Nama Awal',
                    'email' => 'profil@schooly.test',
                ],
            ]);

        // PUT Profile
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/profile', [
                'nama' => 'Nama Baru',
                'no_hp' => '0822222222',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nama' => 'Nama Baru',
                    'email' => 'profil@schooly.test', // email tidak berubah
                    'no_hp' => '0822222222',
                ],
            ]);
    }

    public function test_change_password_success_and_revokes_other_tokens(): void
    {
        $user = User::factory()->create([
            'password' => 'oldpassword123',
            'status_akun' => StatusAkun::AKTIF,
        ]);

        $token1 = $user->createToken('token-1', ['user'])->plainTextToken;
        $token2 = $user->createToken('token-2', ['user'])->plainTextToken;

        // Ubah password via token2
        $response = $this->withHeader('Authorization', 'Bearer ' . $token2)
            ->putJson('/api/v1/profile/password', [
                'password_lama' => 'oldpassword123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Kata sandi berhasil diubah.']);

        // Token 2 masih aktif
        $this->withHeader('Authorization', 'Bearer ' . $token2)
            ->getJson('/api/v1/profile')
            ->assertStatus(200);

        // Token 1 dicabut (401)
        $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->getJson('/api/v1/profile')
            ->assertStatus(401);

        // Tercatat di log_aktivitas
        $this->assertDatabaseHas('log_aktivitas', [
            'aktor_tipe' => 'user',
            'aktor_id' => $user->id_user,
            'aksi' => 'ubah_kata_sandi',
        ]);
    }

    public function test_change_password_fails_if_old_password_wrong(): void
    {
        $user = User::factory()->create([
            'password' => 'oldpassword123',
            'status_akun' => StatusAkun::AKTIF,
        ]);
        $token = $user->createToken('user-token', ['user'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/profile/password', [
                'password_lama' => 'wrongpass',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password_lama']]);
    }

    public function test_admin_login_success_and_logs_activity(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin Real',
            'email' => 'admin@schooly.test',
            'password' => 'secretadmin',
        ]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@schooly.test',
            'password' => 'secretadmin',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'admin' => ['id_admin', 'nama', 'email'],
                ],
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'aktor_tipe' => 'admin',
            'aktor_id' => $admin->id_admin,
            'aksi' => 'login_berhasil',
        ]);
    }

    public function test_admin_login_fails_and_logs_activity(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin Real',
            'email' => 'admin@schooly.test',
            'password' => 'secretadmin',
        ]);

        $response = $this->postJson('/api/v1/admin/auth/login', [
            'email' => 'admin@schooly.test',
            'password' => 'wrongadminpass',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email atau kata sandi salah.',
            ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'aktor_tipe' => 'admin',
            'aktor_id' => $admin->id_admin,
            'aksi' => 'login_gagal',
        ]);
    }

    public function test_admin_logout_revokes_token(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin Real',
            'email' => 'admin@schooly.test',
            'password' => 'secretadmin',
        ]);
        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/admin/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil.']);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/admin/auth/me')
            ->assertStatus(401);
    }
}
