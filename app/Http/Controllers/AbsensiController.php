<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\JadwalSekolah;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId = $request->get('kelas_id');
        
        $absensiQuery = Absensi::with(['murid.kelas', 'guru'])
            ->whereDate('tanggal', $tanggal);
            
        if ($kelasId) {
            $absensiQuery->whereHas('murid', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        
        $absensis = $absensiQuery->latest()->paginate(20);
        $kelas = Kelas::all();
        
        return view('absensi.index', compact('absensis', 'kelas', 'tanggal', 'kelasId'));
    }

    public function create()
    {
        $murid = Murid::with('kelas')->where('is_active', true)->get();
        $guru = Guru::where('is_active', true)->get();
        $kelas = Kelas::all();
        $jadwal = JadwalSekolah::with(['kelas', 'guru'])->where('is_active', true)->get();
        
        return view('absensi.create', compact('murid', 'guru', 'kelas', 'jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'murid_id' => 'nullable|exists:murid,id',
            'guru_id' => 'nullable|exists:guru,id',
            'jadwal_sekolah_id' => 'nullable|exists:jadwal_sekolah,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'waktu_masuk' => 'nullable',
            'waktu_keluar' => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        Absensi::create($request->all());
        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan');
    }

    public function show(Absensi $absensi)
    {
        return view('absensi.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        $murid = Murid::with('kelas')->where('is_active', true)->get();
        $guru = Guru::where('is_active', true)->get();
        $kelas = Kelas::all();
        $jadwal = JadwalSekolah::with(['kelas', 'guru'])->where('is_active', true)->get();
        
        return view('absensi.edit', compact('absensi', 'murid', 'guru', 'kelas', 'jadwal'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $request->validate([
            'murid_id' => 'nullable|exists:murid,id',
            'guru_id' => 'nullable|exists:guru,id',
            'jadwal_sekolah_id' => 'nullable|exists:jadwal_sekolah,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'waktu_masuk' => 'nullable',
            'waktu_keluar' => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        $absensi->update($request->all());
        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui');
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus');
    }
}