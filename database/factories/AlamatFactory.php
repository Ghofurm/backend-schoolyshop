<?php

namespace Database\Factories;

use App\Models\Alamat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alamat>
 */
class AlamatFactory extends Factory
{
    protected $model = Alamat::class;

    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'nama_penerima' => fake()->name(),
            'no_hp_penerima' => fake()->phoneNumber(),
            'alamat_lengkap' => fake()->address(),
            'area_id_biteship' => 'IDNP10IDNC393IDND4703IDZ50249',
            'kode_pos' => '50249',
            'alamat_utama' => true,
        ];
    }
}
