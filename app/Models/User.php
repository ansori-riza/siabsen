<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sekolah_id',
        'role',
        'guru_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_OPERATOR = 'operator';
    const ROLE_PEMBINA = 'pembina';
    const ROLE_PENGELOLA = 'pengelola';
    const ROLE_PIMPINAN = 'pimpinan';

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function isSuperAdmin(): bool
    {
        return $this->normalizedRole() === self::ROLE_SUPER_ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->normalizedRole() === self::ROLE_OPERATOR;
    }

    public function isWaliKelas(): bool
    {
        return $this->normalizedRole() === self::ROLE_PEMBINA;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->normalizedRole() === self::ROLE_PIMPINAN;
    }

    public function normalizedRole(): ?string
    {
        return UserRole::fromStoredRole((string) $this->role)?->value;
    }

    public function roleEnum(): ?UserRole
    {
        return UserRole::fromStoredRole((string) $this->role);
    }

    public function permissions(): array
    {
        return $this->roleEnum()?->permissions() ?? [];
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    public function canAccessSekolah(Sekolah $sekolah): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->sekolah_id === $sekolah->id;
    }

    public function canEditAbsensi(): bool
    {
        return $this->hasPermission('edit_absensi');
    }
}
