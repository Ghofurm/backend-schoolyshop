<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Penjual;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        return [
            'id_penjual' => Penjual::factory(),
            'id_kategori' => Kategori::factory(),
            'id_pengajuan' => null,
            'nama_produk' => fake()->words(3, true),
            'deskripsi' => fake()->paragraph(),
            'harga' => fake()->numberBetween(5000, 150000),
            'stok' => fake()->numberBetween(5, 50),
            'berat_gram' => fake()->numberBetween(100, 1000),
            'foto' => 'produk/default.jpg',
            'status_aktif' => true,
        ];
    }
}
