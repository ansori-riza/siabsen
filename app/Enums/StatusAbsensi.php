<?php

namespace App\Enums;

enum StatusAbsensi: string
{
    case HADIR = 'hadir';
    case TERLAMBAT = 'terlambat';
    case ALPHA = 'alpha';

    public function label(): string
    {
        return match($this) {
            self::HADIR => 'Hadir',
            self::TERLAMBAT => 'Terlambat',
            self::ALPHA => 'Alpha',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::HADIR => 'success',
            self::TERLAMBAT => 'warning',
            self::ALPHA => 'danger',
        };
    }
}
