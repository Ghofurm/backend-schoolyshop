<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';

    protected $fillable = [
        'id_pesanan',
        'id_admin',
        'biteship_order_id',
        'kurir',
        'layanan',
        'no_resi',
        'biaya_kirim',
        'status_pengiriman',
        'tgl_kirim',
        'tgl_diterima',
    ];

    protected function casts(): array
    {
        return [
            'biaya_kirim' => 'integer',
            'tgl_kirim' => 'datetime',
            'tgl_diterima' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function riwayatPengiriman(): HasMany
    {
        return $this->hasMany(RiwayatPengiriman::class, 'id_pengiriman', 'id_pengiriman');
    }
}
