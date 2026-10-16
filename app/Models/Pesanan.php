<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_user',
        'id_alamat',
        'kode_pesanan',
        'subtotal',
        'ongkir',
        'total_bayar',
        'status_pesanan',
        'jenis_konfirmasi',
        'kurir_dipilih',
        'layanan_dipilih',
        'tgl_pesanan',
        'tgl_selesai',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'ongkir' => 'integer',
            'total_bayar' => 'integer',
            'status_pesanan' => StatusPesanan::class,
            'tgl_pesanan' => 'datetime',
            'tgl_selesai' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamat');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function pengiriman(): HasOne
    {
        return $this->hasOne(Pengiriman::class, 'id_pesanan', 'id_pesanan');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(Retur::class, 'pesanan_id', 'id_pesanan');
    }
}
