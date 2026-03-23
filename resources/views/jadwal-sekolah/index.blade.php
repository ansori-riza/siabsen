@extends('layouts.adminlte')

@section('title', 'Jadwal Sekolah')
@section('page-title', 'Jadwal Sekolah')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Jadwal Sekolah</h3>
        <div class="card-tools">
            <a href="{{ route('jadwal-sekolah.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->hari }}</td>
                    <td>{{ $jadwal->jam_mulai }}</td>
                    <td>{{ $jadwal->jam_selesai }}</td>
                    <td>{{ $jadwal->keterangan }}</td>
                    <td>
                        <a href="{{ route('jadwal-sekolah.edit', $jadwal->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('jadwal-sekolah.destroy', $jadwal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Belum ada jadwal</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection