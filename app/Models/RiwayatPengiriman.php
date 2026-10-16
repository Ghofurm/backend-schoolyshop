<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPengiriman extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pengiriman';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'id_pengiriman',
        'status',
        'keterangan',
        'waktu_update',
    ];

    protected function casts(): array
    {
        return [
            'waktu_update' => 'datetime',
        ];
    }

    public function pengiriman(): BelongsTo
    {
        return $this->belongsTo(Pengiriman::class, 'id_pengiriman', 'id_pengiriman');
    }
}
