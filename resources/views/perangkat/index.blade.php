@extends('layouts.adminlte')

@section('title', 'Data Perangkat')
@section('page-title', 'Data Perangkat')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-mobile-alt"></i> Perangkat Absensi
        </h3>
        <div class="card-tools">
            <a href="{{ route('perangkat.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Perangkat
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Nama Perangkat</th>
                    <th>Vendor</th>
                    <th>Tipe</th>
                    <th>Kelas</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perangkats as $key => $perangkat)
                <tr>
                    <td>{{ ($perangkats->currentPage() - 1) * $perangkats->perPage() + $key + 1 }}</td>
                    <td>
                        <strong>{{ $perangkat->nama }}</strong>
                        <br><small class="text-muted">Key: {{ substr($perangkat->device_key, 0, 12) }}...</small>
                    </td>
                    <td>
                        @php
                            $vendorLabels = [
                                'esp32' => ['ESP32 Custom', 'primary'],
                                'solution' => ['Solution', 'info'],
                                'zkteco' => ['ZKTeco', 'success'],
                                'hikvision' => ['Hikvision', 'warning'],
                                'other' => ['Lainnya', 'secondary'],
                            ];
                            $vendor = $vendorLabels[$perangkat->vendor_type] ?? ['Unknown', 'secondary'];
                        @endphp
                        <span class="badge badge-{{ $vendor[1] }}">{{ $vendor[0] }}</span>
                        @if($perangkat->vendor_type !== 'esp32')
                            <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Butuh middleware</small>
                        @endif
                    </td>
                    <td>
                        @if($perangkat->tipe === 'gerbang')
                            <span class="badge badge-info"><i class="fas fa-door-open"></i> Gerbang</span>
                        @else
                            <span class="badge badge-success"><i class="fas fa-chalkboard"></i> Kelas</span>
                        @endif
                    </td>
                    <td>{{ $perangkat->kelas->nama ?? '-' }}</td>
                    <td>{{ $perangkat->lokasi ?? '-' }}</td>
                    <td>
                        @if($perangkat->status === 'online')
                            <span class="badge badge-success"><i class="fas fa-circle text-success"></i> Online</span>
                        @elseif($perangkat->status === 'maintenance')
                            <span class="badge badge-warning"><i class="fas fa-wrench"></i> Maintenance</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-circle text-danger"></i> Offline</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('perangkat.edit', $perangkat->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('perangkat.destroy', $perangkat->id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Yakin hapus perangkat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada perangkat terdaftar.</p>
                        <a href="{{ route('perangkat.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus"></i> Tambah Perangkat Pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($perangkats->hasPages())
    <div class="card-footer">
        {{ $perangkats->links() }}
    </div>
    @endif
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Informasi Vendor</h5>
            <ul class="mb-0">
                <li><strong>ESP32 Custom:</strong> Hardware custom, belum ada middleware</li>
                <li><strong>Solution / ZKTeco / Hikvision:</strong> Perangkat komersial, membutuhkan middleware adapter (Phase 1 Adapter Stream)</li>
            </ul>
        </div>
    </div>
</div>
@endsection
