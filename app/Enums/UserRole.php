<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case OPERATOR = 'operator';
    case WALI_KELAS = 'wali_kelas';
    case KEPALA_SEKOLAH = 'kepala_sekolah';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::OPERATOR => 'Operator TU',
            self::WALI_KELAS => 'Wali Kelas',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
        };
    }

    public function permissions(): array
    {
        return match($this) {
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
            self::WALI_KELAS => [
                'view_absensi',
                'edit_absensi',
                'view_laporan',
                'manage_izin',
            ],
            self::KEPALA_SEKOLAH => [
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
