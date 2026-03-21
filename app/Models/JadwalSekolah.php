<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class JadwalSekolah extends Model
{
    use HasFactory;
    protected $fillable = ['sekolah_id', 'role_target', 'hari', 'jam_masuk', 'jam_pulang', 'toleransi_menit', 'is_active'];
    protected $casts = ['hari' => 'integer', 'toleransi_menit' => 'integer'];
    public function sekolah(): BelongsTo { return $this->belongsTo(Sekolah::class); }
}
