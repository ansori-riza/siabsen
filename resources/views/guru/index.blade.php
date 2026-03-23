@extends('layouts.adminlte')

@section('title', 'Data Guru')
@section('page-title', 'Data Guru')
@section('breadcrumb')
    <li class="breadcrumb-item active">Guru</li>
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
        <form method="GET" action="{{ route('guru.index') }}" class="form-inline">
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Kepegawaian:</label>
                <select name="employment_type" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    @foreach($employmentTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('employment_type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mr-3 mb-2">
                <label class="mr-2">Status:</label>
                <select name="is_active" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            
            <div class="form-group mr-3 mb-2">
                <div class="form-check">
                    <input type="checkbox" name="filter_belum_enroll" class="form-check-input" id="belumEnroll" 
                           {{ request('filter_belum_enroll') ? 'checked' : '' }}>
                    <label class="form-check-label text-danger" for="belumEnroll">
                        <i class="fas fa-exclamation-triangle"></i> Belum Enroll
                    </label>
                </div>
            </div>
            
            <div class="form-group mb-2">
                <button type="submit" class="btn btn-primary btn-sm mr-2">
                    <i class="fas fa-search"></i> Terapkan
                </button>
                <a href="{{ route('guru.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Guru</h3>
        <div class="card-tools">
            <a href="{{ route('guru.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Guru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th width="60">Foto</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Kepegawaian</th>
                    <th>RFID</th>
                    <th>Fingerprint</th>
                    <th>Status</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $key => $guru)
                <tr>
                    <td class="text-center">
                        @if($guru->foto)
                            <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto" class="img-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="img-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $guru->nip ?? '-' }}</td>
                    <td>
                        <strong>{{ $guru->nama }}</strong>
                        @if($guru->email)
                            <br><small class="text-muted">{{ $guru->email }}</small>
                        @endif
                    </td>
                    <td>{{ $guru->jabatan ?? '-' }}</td>
                    <td>
                        @if($guru->employment_type)
                            @php
                                $badgeColors = [
                                    'tetap' => 'success',
                                    'kontrak' => 'info',
                                    'part_time' => 'warning',
                                    'lainnya' => 'secondary',
                                    'tidak_tetap' => 'primary',
                                ];
                                $color = $badgeColors[$guru->employment_type] ?? 'secondary';
                                $labels = [
                                    'tetap' => 'Tetap',
                                    'tidak_tetap' => 'Tidak Tetap',
                                    'kontrak' => 'Kontrak',
                                    'part_time' => 'Part-time',
                                    'lainnya' => 'Lainnya',
                                ];
                            @endphp
                            <span class="badge badge-{{ $color }}">{{ $labels[$guru->employment_type] ?? $guru->employment_type }}</span>
                        @else
                            <span class="badge badge-light">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($guru->rfid_uid)
                            <span class="badge badge-success"><i class="fas fa-check"></i> {{ $guru->rfid_uid }}</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Belum</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($guru->fingerprint_id)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Slot {{ $guru->fingerprint_id }}</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($guru->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus guru ini?')">
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
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                        Data guru kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($gurus->hasPages())
    <div class="card-footer clearfix">
        {{ $gurus->links() }}
    </div>
    @endif
</div>
@endsection
