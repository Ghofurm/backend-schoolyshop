<?php

namespace Database\Factories;

use App\Enums\JenisPenjual;
use App\Enums\StatusPenjual;
use App\Models\Penjual;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penjual>
 */
class PenjualFactory extends Factory
{
    protected $model = Penjual::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'jenis_penjual' => fake()->randomElement([JenisPenjual::SISWA, JenisPenjual::GURU, JenisPenjual::KARYAWAN]),
            'kelas_jabatan' => fake()->randomElement(['X RPL 1', 'XI TKJ 2', 'XII MM 1', 'Guru Produktif', 'Staf TU']),
            'email' => fake()->unique()->safeEmail(),
            'kontak' => fake()->phoneNumber(),
            'status' => StatusPenjual::AKTIF,
        ];
    }
}
