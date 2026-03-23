<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class MuridController extends Controller
{
    public function index()
    {
        $murids = Murid::with(['kelas', 'sekolah'])->paginate(10);
        return view('murid.index', compact('murids'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $sekolahs = Sekolah::all();
        return view('murid.create', compact('kelas', 'sekolahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:murids,nis',
            'email' => 'nullable|email|unique:murids,email',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id',
            'is_active' => 'boolean',
        ]);

        // Auto-set sekolah_id jika tidak ada
        $validated['sekolah_id'] = $request->sekolah_id 
            ?? Sekolah::where('is_active', true)->first()?->id 
            ?? Sekolah::first()?->id;

        /* Auto-set sekolah_id */
        $validated["sekolah_id"] = \App\Models\Sekolah::where("is_active", true)->first()?->id ?? \App\Models\Sekolah::first()?->id;
        Murid::create($validated);
        return redirect()->route('murid.index')->with('success', 'Murid berhasil ditambahkan');
    }

    public function show(Murid $murid)
    {
        return view('murid.show', compact('murid'));
    }

    public function edit(Murid $murid)
    {
        $kelas = Kelas::all();
        $sekolahs = Sekolah::all();
        return view('murid.edit', compact('murid', 'kelas', 'sekolahs'));
    }

    public function update(Request $request, Murid $murid)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|unique:murids,nis,' . $murid->id,
            'email' => 'nullable|email|unique:murids,email,' . $murid->id,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kelas_id' => 'required|exists:kelas,id',
            // sekolah_id auto-set from active school
            'is_active' => 'boolean',
        ]);

        $murid->update($validated);
        return redirect()->route('murid.index')->with('success', 'Murid berhasil diupdate');
    }

    public function destroy(Murid $murid)
    {
        $murid->delete();
        return redirect()->route('murid.index')->with('success', 'Murid berhasil dihapus');
    }
}
