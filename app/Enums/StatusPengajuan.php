<?php

namespace App\Enums;

enum StatusPengajuan: string
{
    case MENUNGGU = 'menunggu';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
}
