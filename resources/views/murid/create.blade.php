@extends('layouts.adminlte')

@section('title', 'Tambah Murid')
@section('page-title', 'Tambah Murid')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('murid.index') }}">Murid</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Murid</h3>
    </div>
    <form action="{{ route('murid.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="nis" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('murid.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection