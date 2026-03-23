<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Perangkat;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class PerangkatController extends Controller
{
    // Vendor types for commercial devices
    const VENDOR_TYPES = [
        'esp32' => 'ESP32 Custom',
        'solution' => 'Solution',
        'zkteco' => 'ZKTeco',
        'hikvision' => 'Hikvision',
        'other' => 'Lainnya',
    ];

    public function index()
    {
        $perangkats = Perangkat::with(['sekolah', 'kelas'])->paginate(10);
        return view('perangkat.index', compact('perangkats'));
    }

    public function create()
    {
        $sekolahs = Sekolah::all();
        $kelas = Kelas::all();
        $vendorTypes = self::VENDOR_TYPES;
        return view('perangkat.create', compact('sekolahs', 'kelas', 'vendorTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tipe' => 'required|in:gerbang,kelas',
            'vendor_type' => 'required|in:esp32,solution,zkteco,hikvision,other',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        // Validasi: jika tipe='kelas', kelas_id wajib
        if ($validated['tipe'] === 'kelas' && empty($validated['kelas_id'])) {
            return back()->withErrors(['kelas_id' => 'Kelas wajib dipilih jika tipe perangkat adalah Kelas'])->withInput();
        }

        // Auto-set sekolah_id dan generate device_key
        $validated['sekolah_id'] = Sekolah::where('is_active', true)->first()?->id ?? Sekolah::first()?->id;
        
        // Generate unique device_key
        $validated['device_key'] = 'DEV-' . strtoupper(uniqid());
        
        // Set defaults
        $validated['status'] = 'offline';

        Perangkat::create($validated);
        return redirect()->route('perangkat.index')->with('success', 'Perangkat berhasil ditambahkan. Device Key: ' . $validated['device_key']);
    }

    public function show(Perangkat $perangkat)
    {
        return view('perangkat.show', compact('perangkat'));
    }

    public function edit(Perangkat $perangkat)
    {
        $sekolahs = Sekolah::all();
        $kelas = Kelas::all();
        $vendorTypes = self::VENDOR_TYPES;
        return view('perangkat.edit', compact('perangkat', 'sekolahs', 'kelas', 'vendorTypes'));
    }

    public function update(Request $request, Perangkat $perangkat)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'tipe' => 'required|in:gerbang,kelas',
            'vendor_type' => 'required|in:esp32,solution,zkteco,hikvision,other',
            'kelas_id' => 'nullable|exists:kelas,id',
            'status' => 'required|in:online,offline,maintenance',
        ]);

        // Validasi: jika tipe='kelas', kelas_id wajib
        if ($validated['tipe'] === 'kelas' && empty($validated['kelas_id'])) {
            return back()->withErrors(['kelas_id' => 'Kelas wajib dipilih jika tipe perangkat adalah Kelas'])->withInput();
        }

        // Jika tipe='gerbang', set kelas_id ke null
        if ($validated['tipe'] === 'gerbang') {
            $validated['kelas_id'] = null;
        }

        $perangkat->update($validated);
        return redirect()->route('perangkat.index')->with('success', 'Perangkat berhasil diupdate');
    }

    public function destroy(Perangkat $perangkat)
    {
        $perangkat->delete();
        return redirect()->route('perangkat.index')->with('success', 'Perangkat berhasil dihapus');
    }
}
