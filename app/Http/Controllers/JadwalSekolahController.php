<?php

namespace App\Http\Controllers;

use App\Models\JadwalSekolah;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalSekolahController extends Controller
{
    public function index()
    {
        $jadwals = JadwalSekolah::with(['kelas', 'sekolah'])->paginate(10);
        return view('jadwal-sekolah.index', compact('jadwals'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $sekolahs = Sekolah::all();
        $gurus = Guru::all();
        return view('jadwal-sekolah.create', compact('kelas', 'sekolahs', 'gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_target' => 'required|in:murid,guru',
            'hari' => 'required|integer|min:1|max:7',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'toleransi_menit' => 'nullable|integer|min:0|max:60',
        ]);

        // Auto-set sekolah_id jika tidak ada
        $validated['sekolah_id'] = $request->sekolah_id 
            ?? Sekolah::where('is_active', true)->first()?->id 
            ?? Sekolah::first()?->id;
        
        $validated['toleransi_menit'] = $validated['toleransi_menit'] ?? 0;

        /* Auto-set sekolah_id */
        $validated["sekolah_id"] = \App\Models\Sekolah::where("is_active", true)->first()?->id ?? \App\Models\Sekolah::first()?->id;
        JadwalSekolah::create($validated);
        return redirect()->route('jadwal-sekolah.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(JadwalSekolah $jadwalSekolah)
    {
        return view('jadwal-sekolah.show', compact('jadwalSekolah'));
    }

    public function edit(JadwalSekolah $jadwalSekolah)
    {
        $kelas = Kelas::all();
        $sekolahs = Sekolah::all();
        $gurus = Guru::all();
        return view('jadwal-sekolah.edit', compact('jadwalSekolah', 'kelas', 'sekolahs', 'gurus'));
    }

    public function update(Request $request, JadwalSekolah $jadwalSekolah)
    {
        $validated = $request->validate([
            'hari' => 'required|string|max:255',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_pelajaran' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            // sekolah_id auto-set from active school
        ]);

        $jadwalSekolah->update($validated);
        return redirect()->route('jadwal-sekolah.index')->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy(JadwalSekolah $jadwalSekolah)
    {
        $jadwalSekolah->delete();
        return redirect()->route('jadwal-sekolah.index')->with('success', 'Jadwal berhasil dihapus');
    }
}
