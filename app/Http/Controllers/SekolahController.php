<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::withCount(['gurus', 'murids', 'kelas'])->paginate(10);
        $institutionTypes = Sekolah::institutionTypeOptions();
        return view('sekolah.index', compact('sekolahs', 'institutionTypes'));
    }

    public function create()
    {
        $institutionTypes = Sekolah::institutionTypeOptions();
        return view('sekolah.create', compact('institutionTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:20|unique:sekolahs,npsn',
            'alamat' => 'nullable|string|max:500',
            'kepala_sekolah' => 'nullable|string|max:255',
            'institution_type' => 'required|in:' . implode(',', array_keys(Sekolah::institutionTypeOptions())),
            'theme_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('sekolah-logos', 'public');
        }

        Sekolah::create($validated);
        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil ditambahkan');
    }

    public function show(Sekolah $sekolah)
    {
        return view('sekolah.show', compact('sekolah'));
    }

    public function edit(Sekolah $sekolah)
    {
        $institutionTypes = Sekolah::institutionTypeOptions();
        return view('sekolah.edit', compact('sekolah', 'institutionTypes'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:20|unique:sekolahs,npsn,' . $sekolah->id,
            'alamat' => 'nullable|string|max:500',
            'kepala_sekolah' => 'nullable|string|max:255',
            'institution_type' => 'required|in:' . implode(',', array_keys(Sekolah::institutionTypeOptions())),
            'theme_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $validated['logo'] = $request->file('logo')->store('sekolah-logos', 'public');
        }

        $sekolah->update($validated);
        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil diupdate');
    }

    public function destroy(Sekolah $sekolah)
    {
        // Delete logo if exists
        if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
            Storage::disk('public')->delete($sekolah->logo);
        }
        
        $sekolah->delete();
        return redirect()->route('sekolah.index')->with('success', 'Sekolah berhasil dihapus');
    }
}
