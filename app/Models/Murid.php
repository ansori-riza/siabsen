<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Murid extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sekolah_id', 'kelas_id', 'nis', 'nama', 'rfid_uid', 'fingerprint_id',
        'jenis_kelamin', 'tanggal_lahir', 'foto', 'nama_ortu', 'hp_ortu', 'alamat', 'is_active'
    ];
    protected $casts = ['tanggal_lahir' => 'date', 'fingerprint_id' => 'integer'];
    public function sekolah(): BelongsTo { return $this->belongsTo(Sekolah::class); }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function absensis(): MorphMany { return $this->morphMany(Absensi::class, 'subject'); }
    public function izins(): MorphMany { return $this->morphMany(Izin::class, 'subject'); }
}
