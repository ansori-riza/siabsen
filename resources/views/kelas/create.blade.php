@extends('layouts.adminlte')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Kelas</h3>
    </div>
    <form action="{{ route('kelas.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: 7A, 8B, 9C" required>
            </div>
            <div class="form-group">
                <label>Tingkat</label>
                <input type="text" name="tingkat" class="form-control" placeholder="Contoh: 7, 8, 9, X, XI, XII" required>
            </div>
            <div class="form-group">
                <label>Wali Kelas</label>
                <select name="wali_kelas_id" class="form-control">
                    <option value="">Pilih Guru</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('kelas.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection