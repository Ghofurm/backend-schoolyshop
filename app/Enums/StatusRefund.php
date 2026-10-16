<?php

namespace App\Enums;

enum StatusRefund: string
{
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case SELESAI_MANUAL = 'selesai_manual';
    case GAGAL = 'gagal';
}
