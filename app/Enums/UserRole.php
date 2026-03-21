<?php

namespace App\Enums;

use App\Models\Sekolah;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case OPERATOR = 'operator';
    case PEMBINA = 'pembina';
    case PENGELOLA = 'pengelola';
    case PIMPINAN = 'pimpinan';

    public static function fromStoredRole(string $role): ?self
    {
        return self::tryFrom($role);
    }

    public function label(): string
    {
        $institutionType = Sekolah::getCurrentInstitutionType();

        $label = config("roles.labels.{$institutionType}.{$this->value}")
            ?? config("roles.labels.default.{$this->value}");

        if (is_string($label) && $label !== '') {
            return $label;
        }

        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::OPERATOR => 'Operator',
            self::PEMBINA => 'Pembina',
            self::PENGELOLA => 'Pengelola',
            self::PIMPINAN => 'Pimpinan',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SUPER_ADMIN => [
                'manage_users',
                'manage_sekolah',
                'manage_guru',
                'manage_murid',
                'manage_kelas',
                'manage_jadwal',
                'manage_perangkat',
                'view_absensi',
                'edit_absensi',
                'view_laporan',
                'manage_izin',
            ],
            self::OPERATOR => [
                'view_absensi',
                'edit_absensi',
                'view_laporan',
                'manage_izin',
            ],
            self::PEMBINA => [
                'view_absensi',
                'edit_absensi',
                'manage_izin',
            ],
            self::PENGELOLA => [
                'view_absensi',
                'manage_izin',
                'view_laporan',
            ],
            self::PIMPINAN => [
                'view_absensi',
                'view_laporan',
            ],
        };
    }

    public function can(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }
}
