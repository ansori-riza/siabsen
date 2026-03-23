@extends('layouts.adminlte')

@section('title', 'Edit Murid')
@section('page-title', 'Edit Murid')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('murid.index') }}">Murid</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Murid</h3>
    </div>
    <form action="{{ route('murid.update', $murid->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="nis" class="form-control" value="{{ $murid->nis }}" required>
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ $murid->nama }}" required>
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <select name="kelas_id" class="form-control" required>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $murid->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="L" {{ $murid->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $murid->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $murid->email }}">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('murid.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection