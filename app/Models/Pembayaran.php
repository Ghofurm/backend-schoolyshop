<?php

namespace App\Models;

use App\Enums\StatusPembayaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pesanan',
        'order_id_midtrans',
        'metode_pembayaran',
        'jumlah_bayar',
        'status_pembayaran',
        'batas_bayar',
        'waktu_bayar',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'integer',
            'status_pembayaran' => StatusPembayaran::class,
            'batas_bayar' => 'datetime',
            'waktu_bayar' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pengembalianDana(): HasMany
    {
        return $this->hasMany(PengembalianDana::class, 'pembayaran_id', 'id_pembayaran');
    }
}
