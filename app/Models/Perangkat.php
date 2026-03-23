<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Perangkat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_id',
        'kelas_id',
        'nama',
        'lokasi',
        'device_key',
        'tipe',
        'tipe_fungsi',
        'vendor_type',
        'ip_address',
        'port',
        'status',
        'last_ping',
        'is_active',
    ];

    protected $casts = [
        'last_ping' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'device_key',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online' 
            && $this->last_ping?->diffInMinutes(now()) < 5;
    }

    public static function generateDeviceKey(): string
    {
        return hash('sha256', Str::random(32));
    }
}