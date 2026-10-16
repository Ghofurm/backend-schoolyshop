<?php

namespace App\Models;

use App\Enums\StatusAkun;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'password',
        'status_akun',
        'tanggal_daftar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_akun' => StatusAkun::class,
            'tanggal_daftar' => 'datetime',
        ];
    }

    public function alamat(): HasMany
    {
        return $this->hasMany(Alamat::class, 'id_user', 'id_user');
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_user', 'id_user');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_user');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(Retur::class, 'user_id', 'id_user');
    }
}
