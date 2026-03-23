@extends('layouts.adminlte')

@section('title', 'Edit Jadwal')
@section('page-title', 'Edit Jadwal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('jadwal-sekolah.index') }}">Jadwal</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Jadwal</h3>
    </div>
    <form action="{{ route('jadwal-sekolah.update', $jadwal->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Hari</label>
                <select name="hari" class="form-control" required>
                    <option value="Senin" {{ $jadwal->hari == 'Senin' ? 'selected' : '' }}>Senin</option>
                    <option value="Selasa" {{ $jadwal->hari == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="Rabu" {{ $jadwal->hari == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="Kamis" {{ $jadwal->hari == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="Jumat" {{ $jadwal->hari == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                    <option value="Sabtu" {{ $jadwal->hari == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $jadwal->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Guru</label>
                <select name="guru_id" class="form-control" required>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ $jadwal->guru_id == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="form-control" value="{{ $jadwal->mata_pelajaran }}" required>
            </div>
            <div class="form-group">
                <label>Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control" value="{{ $jadwal->jam_mulai }}" required>
            </div>
            <div class="form-group">
                <label>Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control" value="{{ $jadwal->jam_selesai }}" required>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('jadwal-sekolah.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection