@extends('layouts.adminlte')

@section('title', 'Tambah Jadwal')
@section('page-title', 'Tambah Jadwal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jadwal-sekolah.index') }}">Jadwal</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Jadwal</h3>
    </div>
    <form action="{{ route('jadwal-sekolah.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Target Role <span class="text-danger">*</span></label>
                <select name="role_target" class="form-control" required>
                    <option value="">Pilih...</option>
                    <option value="murid">Murid</option>
                    <option value="guru">Guru</option>
                </select>
                <small class="form-text text-muted">Pilih jadwal untuk murid atau guru</small>
            </div>
            <div class="form-group">
                <label>Hari <span class="text-danger">*</span></label>
                <select name="hari" class="form-control" required>
                    <option value="">Pilih Hari...</option>
                    <option value="1">Senin</option>
                    <option value="2">Selasa</option>
                    <option value="3">Rabu</option>
                    <option value="4">Kamis</option>
                    <option value="5">Jumat</option>
                    <option value="6">Sabtu</option>
                    <option value="7">Minggu</option>
                </select>
            </div>
            <div class="form-group">
                <label>Jam Masuk <span class="text-danger">*</span></label>
                <input type="time" name="jam_masuk" class="form-control" required>
                <small class="form-text text-muted">Contoh: 07:00</small>
            </div>
            <div class="form-group">
                <label>Jam Pulang <span class="text-danger">*</span></label>
                <input type="time" name="jam_pulang" class="form-control" required>
                <small class="form-text text-muted">Contoh: 15:00</small>
            </div>
            <div class="form-group">
                <label>Toleransi Keterlambatan (menit)</label>
                <input type="number" name="toleransi_menit" class="form-control" value="0" min="0" max="60">
                <small class="form-text text-muted">Default: 0 menit. Contoh: 15 menit</small>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('jadwal-sekolah.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection