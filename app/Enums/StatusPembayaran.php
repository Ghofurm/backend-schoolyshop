<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case MENUNGGU = 'menunggu';
    case BERHASIL = 'berhasil';
    case GAGAL = 'gagal';
    case KEDALUWARSA = 'kedaluwarsa';
}
