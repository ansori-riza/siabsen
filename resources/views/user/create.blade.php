@extends('layouts.adminlte')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-user-plus"></i> Form Tambah User</h3>
            </div>
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Akun -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-user"></i> Informasi Akun</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Konfirmasi Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Role & Relasi -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-user-tag"></i> Role & Relasi</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                            <option value="">Pilih...</option>
                                            @foreach($roles as $key => $label)
                                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Sekolah <span class="text-muted">(opsional)</span></label>
                                        <select name="sekolah_id" class="form-control @error('sekolah_id') is-invalid @enderror">
                                            <option value="">Pilih...</option>
                                            @foreach($sekolahs as $sekolah)
                                                <option value="{{ $sekolah->id }}" {{ old('sekolah_id') == $sekolah->id ? 'selected' : '' }}>{{ $sekolah->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('sekolah_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Untuk non Super Admin, user akan dibatasi aksesnya ke sekolah ini</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Link ke Data Guru <span class="text-muted">(opsional)</span></label>
                                        <select name="guru_id" class="form-control @error('guru_id') is-invalid @enderror">
                                            <option value="">Tidak terhubung</option>
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama }} ({{ $guru->nip ?? 'NIP-' }})</option>
                                            @endforeach
                                        </select>
                                        @error('guru_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Hubungkan akun dengan data guru jika diperlukan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-default">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection