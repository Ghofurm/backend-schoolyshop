<?php

namespace App\Models;

use App\Enums\KategoriAlasanRetur;
use App\Enums\StatusRetur;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';
    protected $primaryKey = 'retur_id';

    protected $fillable = [
        'pesanan_id',
        'detail_pesanan_id',
        'user_id',
        'admin_id',
        'jumlah',
        'kategori_alasan',
        'keterangan',
        'bukti_foto',
        'status',
        'alasan_keputusan',
        'biaya_retur',
        'penanggung_biaya',
        'biteship_order_id_retur',
        'nomor_resi_retur',
        'kondisi_barang',
        'layak_jual',
        'tanggal_pengajuan',
        'tanggal_diterima',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'biaya_retur' => 'integer',
            'layak_jual' => 'boolean',
            'bukti_foto' => 'array',
            'status' => StatusRetur::class,
            'kategori_alasan' => KategoriAlasanRetur::class,
            'tanggal_pengajuan' => 'datetime',
            'tanggal_diterima' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'id_pesanan');
    }

    public function detailPesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPesanan::class, 'detail_pesanan_id', 'id_detail');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id_admin');
    }

    public function riwayatRetur(): HasMany
    {
        return $this->hasMany(RiwayatRetur::class, 'retur_id', 'retur_id');
    }

    public function pengembalianDana(): HasOne
    {
        return $this->hasOne(PengembalianDana::class, 'retur_id', 'retur_id');
    }
}
