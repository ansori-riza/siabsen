<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Kelas extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['sekolah_id', 'nama', 'tingkat', 'wali_kelas_id', 'kapasitas'];
    public function sekolah(): BelongsTo { return $this->belongsTo(Sekolah::class); }
    public function waliKelas(): BelongsTo { return $this->belongsTo(Guru::class, 'wali_kelas_id'); }
    public function murids(): HasMany { return $this->hasMany(Murid::class); }
}
