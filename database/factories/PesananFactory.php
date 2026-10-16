<?php

namespace Database\Factories;

use App\Enums\StatusPesanan;
use App\Models\Alamat;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    protected $model = Pesanan::class;

    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'id_alamat' => function (array $attributes) {
                return Alamat::factory()->create(['id_user' => $attributes['id_user']])->id_alamat;
            },
            'kode_pesanan' => 'ORD-' . strtoupper(Str::random(10)),
            'subtotal' => 50000,
            'ongkir' => 10000,
            'total_bayar' => 60000,
            'status_pesanan' => StatusPesanan::MENUNGGU_PEMBAYARAN,
            'jenis_konfirmasi' => null,
            'kurir_dipilih' => 'jne',
            'layanan_dipilih' => 'reg',
            'tgl_pesanan' => now(),
            'tgl_selesai' => null,
        ];
    }

    public function withDetails(int $count = 1): static
    {
        return $this->afterCreating(function (Pesanan $pesanan) use ($count) {
            $subtotal = 0;

            for ($i = 0; $i < $count; $i++) {
                $produk = Produk::factory()->create();
                $qty = 2;
                $lineSubtotal = $produk->harga * $qty;
                $subtotal += $lineSubtotal;

                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $qty,
                    'harga_satuan' => $produk->harga,
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $pesanan->update([
                'subtotal' => $subtotal,
                'total_bayar' => $subtotal + $pesanan->ongkir,
            ]);
        });
    }
}
