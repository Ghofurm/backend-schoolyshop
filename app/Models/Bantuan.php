<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bantuan extends Model
{
    use HasFactory;

    protected $table = 'bantuan';
    protected $primaryKey = 'id_bantuan';

    protected $fillable = [
        'id_admin',
        'nomor_whatsapp',
        'pesan_awal',
        'tautan_whatsapp',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
