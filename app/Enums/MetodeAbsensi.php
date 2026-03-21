<?php

namespace App\Enums;

enum MetodeAbsensi: string
{
    case RFID = 'rfid';
    case FINGERPRINT = 'fingerprint';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match($this) {
            self::RFID => 'RFID Card',
            self::FINGERPRINT => 'Fingerprint',
            self::MANUAL => 'Input Manual',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::RFID => '🪪',
            self::FINGERPRINT => '👆',
            self::MANUAL => '✏️',
        };
    }
}
