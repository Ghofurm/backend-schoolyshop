<?php

namespace App\Enums;

enum StatusRetur: string
{
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case BARANG_DIKIRIM_BALIK = 'barang_dikirim_balik';
    case BARANG_DITERIMA = 'barang_diterima';
    case SELESAI = 'selesai';
}
