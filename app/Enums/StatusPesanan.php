<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    case DIBAYAR = 'dibayar';
    case DIPROSES = 'diproses';
    case DIKIRIM = 'dikirim';
    case SELESAI = 'selesai';
    case DALAM_PENGEMBALIAN = 'dalam_pengembalian';
    case KEDALUWARSA = 'kedaluwarsa';
    case BERMASALAH = 'bermasalah';
}
