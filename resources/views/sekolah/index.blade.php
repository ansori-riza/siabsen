@extends('layouts.adminlte')

@section('title', 'Data Sekolah')
@section('page-title', 'Data Sekolah')
@section('breadcrumb')
    <li class="breadcrumb-item active">Sekolah</li>
@endsection

@section('content')
<!-- Filter Section -->
<div class="card card-outline card-info mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filter</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('sekolah.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipe Lembaga</label>
                        <select name="institution_type" class="form-control">
                            <option value="">Semua Tipe</option>
                            @foreach($institutionTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('institution_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="{{ route('sekolah.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-school"></i> Daftar Sekolah</h3>
        <div class="card-tools">
            <a href="{{ route('sekolah.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Sekolah
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered">
            <thead class="bg-light">
                <tr>
                    <th width="60">Logo</th>
                    <th>Nama Sekolah</th>
                    <th>NPSN</th>
                    <th>Tipe</th>
                    <th>Theme Color</th>
                    <th>Statistik</th>
                    <th>Status</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sekolahs as $sekolah)
                <tr>
                    <td class="text-center">
                        @if($sekolah->logo)
                            <img src="{{ asset('storage/' . $sekolah->logo) }}" alt="Logo" class="img-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="img-circle d-flex align-items-center justify-content-center bg-secondary" style="width: 50px; height: 50px; margin: 0 auto;">
                                <i class="fas fa-school text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $sekolah->nama }}</strong>
                        @if($sekolah->kepala_sekolah)
                            <br><small class="text-muted">Kepala: {{ $sekolah->kepala_sekolah }}</small>
                        @endif
                    </td>
                    <td>{{ $sekolah->npsn ?: '-' }}</td>
                    <td>
                        @php
                            $typeColors = [
                                'sekolah_umum' => 'primary',
                                'pondok' => 'success',
                                'madrasah' => 'info',
                                'lainnya' => 'secondary',
                            ];
                            $typeColor = $typeColors[$sekolah->institution_type] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $typeColor }}">
                            {{ $institutionTypes[$sekolah->institution_type] ?? 'Lainnya' }}
                        </span>
                    </td>
                    <td>
                        @if($sekolah->theme_color)
                            <div class="d-flex align-items-center">
                                <div style="width: 25px; height: 25px; background: {{ $sekolah->theme_color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                <span class="ml-2 text-muted">{{ $sekolah->theme_color }}</span>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary" title="Guru">
                            <i class="fas fa-chalkboard-teacher"></i> {{ $sekolah->gurus_count }}
                        </span>
                        <span class="badge badge-success" title="Murid">
                            <i class="fas fa-user-graduate"></i> {{ $sekolah->murids_count }}
                        </span>
                        <span class="badge badge-warning" title="Kelas">
                            <i class="fas fa-school"></i> {{ $sekolah->kelas_count }}
                        </span>
                    </td>
                    <td>
                        @if($sekolah->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('sekolah.edit', $sekolah) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('sekolah.destroy', $sekolah) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus sekolah ini? Semua data terkait akan terpengaruh.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Data sekolah kosong
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $sekolahs->links() }}
    </div>
</div>
@endsection
