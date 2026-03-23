<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Guru;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['sekolah', 'guru']);
        
        // Filter: Role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Filter: Sekolah
        if ($request->has('sekolah_id') && $request->sekolah_id) {
            $query->where('sekolah_id', $request->sekolah_id);
        }
        
        $users = $query->paginate(10);
        
        // Role options for filter dropdown
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $roles[$role->value] = $role->label();
        }
        
        $sekolahs = Sekolah::all();
        
        return view('user.index', compact('users', 'roles', 'sekolahs'));
    }

    public function create()
    {
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $roles[$role->value] = $role->label();
        }
        
        $sekolahs = Sekolah::all();
        $gurus = Guru::whereNull('user_id')->where('is_active', true)->get();
        
        return view('user.create', compact('roles', 'sekolahs', 'gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'sekolah_id' => 'nullable|exists:sekolahs,id',
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Create user
        $user = User::create($validated);
        
        // Link guru if provided
        if ($user->guru_id) {
            Guru::where('id', $user->guru_id)->update(['user_id' => $user->id]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        $user->load(['sekolah', 'guru']);
        return view('user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $roles[$role->value] = $role->label();
        }
        
        $sekolahs = Sekolah::all();
        $gurus = Guru::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        return view('user.edit', compact('user', 'roles', 'sekolahs', 'gurus'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'sekolah_id' => 'nullable|exists:sekolahs,id',
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Unlink old guru if changed
        if ($user->guru_id && $user->guru_id != ($validated['guru_id'] ?? null)) {
            Guru::where('id', $user->guru_id)->update(['user_id' => null]);
        }
        
        $user->update($validated);
        
        // Link new guru if provided
        if (!empty($validated['guru_id'])) {
            Guru::where('id', $validated['guru_id'])->update(['user_id' => $user->id]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return redirect()->route('user.index')->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        // Unlink guru
        if ($user->guru_id) {
            Guru::where('id', $user->guru_id)->update(['user_id' => null]);
        }
        
        $user->delete();
        
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }
}