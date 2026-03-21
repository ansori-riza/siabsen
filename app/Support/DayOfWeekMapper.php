<?php

namespace App\Support;

use Carbon\CarbonInterface;

class DayOfWeekMapper
{
    public const LABELS = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public static function options(): array
    {
        return self::LABELS;
    }

    public static function toLabel(int $day): string
    {
        return self::LABELS[$day] ?? 'Tidak diketahui';
    }

    public static function today(?CarbonInterface $date = null): int
    {
        return ($date ?? now())->dayOfWeek;
    }
}
