<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Sekolah extends Model
{
    use HasFactory;

    public const INSTITUTION_TYPE_SEKOLAH_UMUM = 'sekolah_umum';
    public const INSTITUTION_TYPE_PONDOK = 'pondok';
    public const INSTITUTION_TYPE_MADRASAH = 'madrasah';
    public const INSTITUTION_TYPE_LAINNYA = 'lainnya';

    protected $fillable = ['nama', 'npsn', 'alamat', 'kepala_sekolah', 'institution_type', 'logo', 'theme_color', 'is_active'];

    public static function institutionTypeOptions(): array
    {
        return [
            self::INSTITUTION_TYPE_SEKOLAH_UMUM => 'Sekolah Umum',
            self::INSTITUTION_TYPE_PONDOK => 'Pondok',
            self::INSTITUTION_TYPE_MADRASAH => 'Madrasah',
            self::INSTITUTION_TYPE_LAINNYA => 'Lainnya',
        ];
    }

    public static function getCurrentInstitutionType(): string
    {
        if (! Schema::hasColumn('sekolahs', 'institution_type')) {
            return self::INSTITUTION_TYPE_SEKOLAH_UMUM;
        }

        $user = Auth::user();
        $sekolah = $user?->sekolah;

        if (! $sekolah instanceof self) {
            $sekolah = self::query()->where('is_active', true)->first() ?? self::query()->first();
        }

        return $sekolah?->institution_type ?: self::INSTITUTION_TYPE_SEKOLAH_UMUM;
    }

    public static function getEducatorLabel(): string
    {
        return self::getGuruLabel();
    }

    public static function getLabelDictionary(): array
    {
        return match (self::getCurrentInstitutionType()) {
            self::INSTITUTION_TYPE_PONDOK,
            self::INSTITUTION_TYPE_MADRASAH => [
                'guru_label' => 'Ustadz/Pengajar',
                'class_guardian_label' => 'Musyrif Kelas',
                'student_label' => 'Murid',
            ],
            default => [
                'guru_label' => 'Guru',
                'class_guardian_label' => 'Wali Kelas',
                'student_label' => 'Murid',
            ],
        };
    }

    public static function getGuruLabel(): string
    {
        return self::getLabelDictionary()['guru_label'];
    }

    public static function getClassGuardianLabel(): string
    {
        return self::getLabelDictionary()['class_guardian_label'];
    }

    public static function getStudentLabel(): string
    {
        return self::getLabelDictionary()['student_label'];
    }

    public function gurus(): HasMany { return $this->hasMany(Guru::class); }
    public function murids(): HasMany { return $this->hasMany(Murid::class); }
    public function kelas(): HasMany { return $this->hasMany(Kelas::class); }
    public function jadwalSekolahs(): HasMany { return $this->hasMany(JadwalSekolah::class); }
    public function perangkats(): HasMany { return $this->hasMany(Perangkat::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
}
