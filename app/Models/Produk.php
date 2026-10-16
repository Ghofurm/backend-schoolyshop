<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_penjual',
        'id_kategori',
        'id_pengajuan',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'berat_gram',
        'foto',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'berat_gram' => 'integer',
            'status_aktif' => 'boolean',
        ];
    }

    public function penjual(): BelongsTo
    {
        return $this->belongsTo(Penjual::class, 'id_penjual', 'id_penjual');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function pengajuanProduk(): BelongsTo
    {
        return $this->belongsTo(PengajuanProduk::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_produk', 'id_produk');
    }
}
