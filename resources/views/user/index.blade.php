@extends('layouts.adminlte')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb')
    <li class="breadcrumb-item active">User</li>
@endsection

@section('content')
<!-- Filter Section -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filter</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('user.index') }}" class="form-inline">
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Role:</label>
                <select name="role" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Sekolah:</label>
                <select name="sekolah_id" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach($sekolahs as $sekolah)
                        <option value="{{ $sekolah->id }}" {{ request('sekolah_id') == $sekolah->id ? 'selected' : '' }}>
                            {{ $sekolah->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-2">
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-search"></i> Terapkan
                </button>
                <a href="{{ route('user.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar User</h3>
        <div class="card-tools">
            <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Sekolah</th>
                    <th>Guru</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->name }}</strong>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $badgeColors = [
                                'super_admin' => 'danger',
                                'operator' => 'success',
                                'pembina' => 'info',
                                'pengelola' => 'warning',
                                'pimpinan' => 'primary',
                            ];
                            $color = $badgeColors[$user->role] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $color }}">
                            {{ App\Enums\UserRole::tryFrom($user->role)?->label() ?? $user->role }}
                        </span>
                    </td>
                    <td>{{ $user->sekolah->nama ?? '-' }}</td>
                    <td>{{ $user->guru->nama ?? '-' }}</td>
                    <td>
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                        Data user kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer clearfix">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection