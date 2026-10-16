<?php

namespace Database\Factories;

use App\Enums\StatusPembayaran;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    public function definition(): array
    {
        return [
            'id_pesanan' => Pesanan::factory(),
            'order_id_midtrans' => 'MID-' . strtoupper(Str::random(12)),
            'metode_pembayaran' => 'gopay',
            'jumlah_bayar' => 60000,
            'status_pembayaran' => StatusPembayaran::MENUNGGU,
            'batas_bayar' => now()->addMinutes(60),
            'waktu_bayar' => null,
        ];
    }
}
