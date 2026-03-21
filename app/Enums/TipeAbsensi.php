<?php

namespace App\Enums;

enum TipeAbsensi: string
{
    case MASUK = 'masuk';
    case PULANG = 'pulang';

    public function label(): string
    {
        return match($this) {
            self::MASUK => 'Masuk',
            self::PULANG => 'Pulang',
        };
    }
}
