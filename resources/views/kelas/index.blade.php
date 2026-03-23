@extends('layouts.adminlte')

@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')
@section('breadcrumb')
    <li class="breadcrumb-item active">Kelas</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kelas</h3>
        <div class="card-tools">
            <a href="{{ route('kelas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kelas
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Wali Kelas</th>
                    <th>Jumlah Murid</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $key => $k)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>{{ $k->tingkat }}</td>
                    <td>{{ $k->waliKelas->nama ?? '-' }}</td>
                    <td>{{ $k->murids_count ?? 0 }}</td>
                    <td>
                        <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
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
                    <td colspan="6" class="text-center">Data kelas kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection