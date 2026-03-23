<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['waliKelas', 'sekolah'])->paginate(10);
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $gurus = Guru::all();
        $sekolahs = Sekolah::all();
        return view('kelas.create', compact('gurus', 'sekolahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'required',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'is_active' => 'boolean',
        ]);

        // Auto-set sekolah_id jika tidak ada
        $validated['sekolah_id'] = $request->sekolah_id 
            ?? Sekolah::where('is_active', true)->first()?->id 
            ?? Sekolah::first()?->id;

        /* Auto-set sekolah_id */
        $validated["sekolah_id"] = \App\Models\Sekolah::where("is_active", true)->first()?->id ?? \App\Models\Sekolah::first()?->id;
        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas)
    {
        return view('kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        $gurus = Guru::all();
        $sekolahs = Sekolah::all();
        return view('kelas.edit', compact('kelas', 'gurus', 'sekolahs'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'required',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            // sekolah_id auto-set from active school
            'is_active' => 'boolean',
        ]);

        $kelas->update($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}
