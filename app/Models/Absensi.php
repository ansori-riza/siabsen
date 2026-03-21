<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_id',
        'perangkat_id',
        'subject_type',
        'subject_id',
        'tipe',
        'status',
        'metode',
        'waktu_absen',
        'keterangan',
        'dinput_oleh',
    ];

    protected $casts = [
        'waktu_absen' => 'datetime',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function perangkat(): BelongsTo
    {
        return $this->belongsTo(Perangkat::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinput_oleh');
    }
}