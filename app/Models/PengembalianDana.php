<?php

namespace App\Models;

use App\Enums\StatusRefund;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengembalianDana extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_dana';
    protected $primaryKey = 'refund_id';

    protected $fillable = [
        'retur_id',
        'pembayaran_id',
        'nominal_barang',
        'potongan_biaya_retur',
        'nominal_ongkir_dikembalikan',
        'nominal_refund',
        'metode_refund',
        'refund_id_midtrans',
        'rekening_tujuan',
        'status',
        'tanggal_proses',
    ];

    protected function casts(): array
    {
        return [
            'nominal_barang' => 'integer',
            'potongan_biaya_retur' => 'integer',
            'nominal_ongkir_dikembalikan' => 'integer',
            'nominal_refund' => 'integer',
            'status' => StatusRefund::class,
            'tanggal_proses' => 'datetime',
        ];
    }

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'retur_id', 'retur_id');
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id', 'id_pembayaran');
    }
}
