<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Sekolah extends Model
{
    use HasFactory;
    protected $fillable = ['nama', 'npsn', 'alamat', 'kepala_sekolah', 'logo', 'theme_color', 'is_active'];
    public function gurus(): HasMany { return $this->hasMany(Guru::class); }
    public function murids(): HasMany { return $this->hasMany(Murid::class); }
    public function kelas(): HasMany { return $this->hasMany(Kelas::class); }
    public function jadwalSekolahs(): HasMany { return $this->hasMany(JadwalSekolah::class); }
    public function perangkats(): HasMany { return $this->hasMany(Perangkat::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
}
