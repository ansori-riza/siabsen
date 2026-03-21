<?php

namespace App\Enums;

enum RoleTarget: string
{
    case MURID = 'murid';
    case GURU = 'guru';

    public function label(): string
    {
        return match($this) {
            self::MURID => 'Murid',
            self::GURU => 'Guru',
        };
    }
}
