<?php

namespace Database\Factories;

use App\Enums\StatusAkun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'status_akun' => StatusAkun::AKTIF,
            'tanggal_daftar' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
