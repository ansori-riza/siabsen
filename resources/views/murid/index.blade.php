@extends('layouts.adminlte')

@section('title', 'Data Murid')
@section('page-title', 'Data Murid')
@section('breadcrumb')
    <li class="breadcrumb-item active">Murid</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Murid</h3>
        <div class="card-tools">
            <a href="{{ route('murid.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Murid
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jenis Kelamin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($murids as $key => $murid)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $murid->nis }}</td>
                    <td>{{ $murid->nama }}</td>
                    <td>{{ $murid->kelas->nama ?? '-' }}</td>
                    <td>{{ $murid->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>
                        <a href="{{ route('murid.edit', $murid->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('murid.destroy', $murid->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
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
                    <td colspan="6" class="text-center">Data murid kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection