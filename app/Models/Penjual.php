<?php

namespace App\Models;

use App\Enums\JenisPenjual;
use App\Enums\StatusPenjual;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjual extends Model
{
    use HasFactory;

    protected $table = 'penjual';
    protected $primaryKey = 'id_penjual';

    protected $fillable = [
        'nama',
        'jenis_penjual',
        'kelas_jabatan',
        'email',
        'kontak',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jenis_penjual' => JenisPenjual::class,
            'status' => StatusPenjual::class,
        ];
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_penjual', 'id_penjual');
    }

    public function pengajuanProduk(): HasMany
    {
        return $this->hasMany(PengajuanProduk::class, 'id_penjual', 'id_penjual');
    }
}
