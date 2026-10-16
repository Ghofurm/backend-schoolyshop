<?php

namespace App\Models;

use App\Enums\StatusPengajuan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanProduk extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_produk';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'id_penjual',
        'id_admin',
        'id_kategori',
        'nama_produk',
        'deskripsi',
        'harga_usulan',
        'stok_usulan',
        'foto',
        'status_pengajuan',
        'alasan_penolakan',
        'tgl_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'harga_usulan' => 'integer',
            'stok_usulan' => 'integer',
            'status_pengajuan' => StatusPengajuan::class,
            'tgl_pengajuan' => 'datetime',
        ];
    }

    public function penjual(): BelongsTo
    {
        return $this->belongsTo(Penjual::class, 'id_penjual', 'id_penjual');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function produk(): HasOne
    {
        return $this->hasOne(Produk::class, 'id_pengajuan', 'id_pengajuan');
    }
}
