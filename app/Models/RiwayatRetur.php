<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatRetur extends Model
{
    use HasFactory;

    protected $table = 'riwayat_retur';

    protected $fillable = [
        'retur_id',
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

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'retur_id', 'retur_id');
    }
}
