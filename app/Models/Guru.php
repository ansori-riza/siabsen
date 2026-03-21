<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Guru extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sekolah_id', 'nip', 'nama', 'rfid_uid', 'fingerprint_id',
        'status', 'jabatan', 'hp', 'email', 'foto', 'is_active'
    ];
    protected $casts = ['fingerprint_id' => 'integer'];
    public function sekolah(): BelongsTo { return $this->belongsTo(Sekolah::class); }
    public function kelas(): HasOne { return $this->hasOne(Kelas::class, 'wali_kelas_id'); }
    public function absensis(): MorphMany { return $this->morphMany(Absensi::class, 'subject'); }
    public function izins(): MorphMany { return $this->morphMany(Izin::class, 'subject'); }
}
