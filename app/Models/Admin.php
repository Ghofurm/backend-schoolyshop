<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function pengajuanProduk(): HasMany
    {
        return $this->hasMany(PengajuanProduk::class, 'id_admin', 'id_admin');
    }

    public function pengiriman(): HasMany
    {
        return $this->hasMany(Pengiriman::class, 'id_admin', 'id_admin');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(Retur::class, 'admin_id', 'id_admin');
    }

    public function bantuan(): HasMany
    {
        return $this->hasMany(Bantuan::class, 'id_admin', 'id_admin');
    }
}
