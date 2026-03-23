@extends('layouts.adminlte')

@section('title', 'Monitoring Absensi')
@section('page-title', 'Monitoring Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item active">Absensi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Filter Absensi</h3>
                <span class="badge badge-info">
                    <i class="fas fa-sync-alt"></i> Auto-refresh: 30 detik
                </span>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('absensi.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="kelas_id" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" onclick="window.location.reload();" class="btn btn-success btn-block">
                                    <i class="fas fa-sync-alt"></i> Refresh Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Absensi Hari Ini</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="absensiTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Sebagai</th>
                    <th>Tipe</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Perangkat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $key => $absensi)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $absensi->subject->nama ?? 'Unknown' }}</td>
                    <td>
                        @if($absensi->subject_type == 'App\Models\Guru')
                            <span class="badge badge-success">Guru</span>
                        @else
                            <span class="badge badge-info">Murid</span>
                        @endif
                    </td>
                    <td>
                        @if($absensi->tipe == 'masuk')
                            <span class="badge badge-success">Masuk</span>
                        @else
                            <span class="badge badge-warning">Pulang</span>
                        @endif
                    </td>
                    <td>{{ $absensi->waktu_absen ? \Carbon\Carbon::parse($absensi->waktu_absen)->format('H:i:s') : '-' }}</td>
                    <td>
                        @if($absensi->status == 'hadir')
                            <span class="badge badge-success">Hadir</span>
                        @elseif($absensi->status == 'terlambat')
                            <span class="badge badge-warning">Terlambat</span>
                        @elseif($absensi->status == 'izin')
                            <span class="badge badge-info">Izin</span>
                        @elseif($absensi->status == 'sakit')
                            <span class="badge badge-primary">Sakit</span>
                        @else
                            <span class="badge badge-danger">Alpha</span>
                        @endif
                    </td>
                    <td>
                        @if($absensi->metode == 'rfid')
                            <span class="badge badge-info">RFID</span>
                        @elseif($absensi->metode == 'fingerprint')
                            <span class="badge badge-success">Fingerprint</span>
                        @else
                            <span class="badge badge-secondary">Manual</span>
                        @endif
                    </td>
                    <td>{{ $absensi->perangkat->nama ?? 'Manual' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Data absensi kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<!-- Auto-refresh setiap 30 detik seperti di Filament -->
<script>
    $(document).ready(function() {
        // Auto refresh page every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
        
        // Update countdown timer
        var timeLeft = 30;
        setInterval(function() {
            timeLeft--;
            if(timeLeft <= 0) timeLeft = 30;
            // Optionally display countdown somewhere
        }, 1000);
    });
</script>
@endpush