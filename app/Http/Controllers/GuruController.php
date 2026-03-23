<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with('sekolah');
        
        // Filter: Belum Enroll
        if ($request->has('filter_belum_enroll')) {
            $query->whereNull('rfid_uid')->whereNull('fingerprint_id');
        }
        
        // Filter: Employment Type
        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }
        
        // Filter: Status Aktif
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        $gurus = $query->paginate(10);
        
        // Employment type options for filter dropdown
        $employmentTypes = [
            'tetap' => 'Tetap',
            'tidak_tetap' => 'Tidak Tetap',
            'kontrak' => 'Kontrak',
            'part_time' => 'Part-time',
            'lainnya' => 'Lainnya',
        ];
        
        return view('guru.index', compact('gurus', 'employmentTypes'));
    }

    public function create()
    {
        $employmentTypes = [
            'tetap' => 'Tetap',
            'tidak_tetap' => 'Tidak Tetap',
            'kontrak' => 'Kontrak',
            'part_time' => 'Part-time',
            'lainnya' => 'Lainnya',
        ];
        
        return view('guru.create', compact('employmentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:100',
            'employment_type' => 'required|in:tetap,tidak_tetap,kontrak,part_time,lainnya',
            'employment_detail' => 'nullable|string|max:255',
            'rfid_uid' => 'nullable|string|max:20|unique:gurus,rfid_uid',
            'fingerprint_id' => 'nullable|integer|min:1|max:162|unique:gurus,fingerprint_id',
            'hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:gurus,email',
            // sekolah_id auto-set from active school
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('guru-fotos', 'public');
        }

        // Auto-set sekolah_id from active school
$validated['sekolah_id'] = Sekolah::where('is_active', true)->first()?->id ?? Sekolah::first()?->id;
Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan');
    }

    public function show(Guru $guru)
    {
        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
        $employmentTypes = [
            'tetap' => 'Tetap',
            'tidak_tetap' => 'Tidak Tetap',
            'kontrak' => 'Kontrak',
            'part_time' => 'Part-time',
            'lainnya' => 'Lainnya',
        ];
        
        return view('guru.edit', compact('guru', 'employmentTypes'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:gurus,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:100',
            'employment_type' => 'required|in:tetap,tidak_tetap,kontrak,part_time,lainnya',
            'employment_detail' => 'nullable|string|max:255',
            'rfid_uid' => 'nullable|string|max:20|unique:gurus,rfid_uid,' . $guru->id,
            'fingerprint_id' => 'nullable|integer|min:1|max:162|unique:gurus,fingerprint_id,' . $guru->id,
            'hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:gurus,email,' . $guru->id,
            // sekolah_id auto-set from active school
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }
            $validated['foto'] = $request->file('foto')->store('guru-fotos', 'public');
        }

        // Keep existing sekolah_id or set from active school
if (!$guru->sekolah_id) {
    $validated['sekolah_id'] = Sekolah::where('is_active', true)->first()?->id ?? Sekolah::first()?->id;
}
$guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil diupdate');
    }

    public function destroy(Guru $guru)
    {
        // Delete foto if exists
        if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }
        
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus');
    }
}
