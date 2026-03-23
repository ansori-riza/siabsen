@extends('layouts.adminlte')

@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.index') }}">Guru</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Guru</h3>
            </div>
            <form action="{{ route('guru.update', $guru) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Pribadi -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-id-card"></i> Informasi Pribadi</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>NIP <span class="text-muted">(opsional)</span></label>
                                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}">
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $guru->nama) }}" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Jabatan <span class="text-muted">(opsional)</span></label>
                                        <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $guru->jabatan) }}">
                                        @error('jabatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kepegawaian & Status -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-briefcase"></i> Kepegawaian</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Jenis Kepegawaian <span class="text-danger">*</span></label>
                                        <select name="employment_type" class="form-control @error('employment_type') is-invalid @enderror" required>
                                            <option value="">Pilih...</option>
                                            @foreach($employmentTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('employment_type', $guru->employment_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('employment_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Detail Kepegawaian <span class="text-muted">(opsional)</span></label>
                                        <input type="text" name="employment_detail" class="form-control @error('employment_detail') is-invalid @enderror" 
                                               value="{{ old('employment_detail', $guru->employment_detail) }}" placeholder="Contoh: Guru Tetap, Wali Kelas">
                                        @error('employment_detail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', $guru->is_active) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="isActive">Status Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Kartu & Biometrik -->
                        <div class="col-md-6">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-id-badge"></i> Kartu & Biometrik</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-wifi text-primary"></i> RFID UID
                                            <span class="text-muted">(opsional)</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="rfid_uid" class="form-control @error('rfid_uid') is-invalid @enderror" 
                                                   value="{{ old('rfid_uid', $guru->rfid_uid) }}" placeholder="Scan kartu RFID">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-primary text-white">
                                                    <i class="fas fa-qrcode"></i> Scan
                                                </span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Tempelkan kartu RFID ke reader untuk scan otomatis</small>
                                        @error('rfid_uid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-fingerprint text-success"></i> Fingerprint ID (Slot 1-162)
                                            <span class="text-muted">(opsional)</span>
                                        </label>
                                        <input type="number" name="fingerprint_id" class="form-control @error('fingerprint_id') is-invalid @enderror" 
                                               value="{{ old('fingerprint_id', $guru->fingerprint_id) }}" min="1" max="162" placeholder="Enroll fingerprint di device">
                                        <small class="form-text text-muted">Masukkan slot fingerprint (1-162) setelah enroll di device</small>
                                        @error('fingerprint_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kontak & Foto -->
                        <div class="col-md-6">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-address-book"></i> Kontak & Foto</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>No. HP <span class="text-muted">(opsional)</span></label>
                                        <input type="tel" name="hp" class="form-control @error('hp') is-invalid @enderror" value="{{ old('hp', $guru->hp) }}">
                                        @error('hp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="text-muted">(opsional)</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $guru->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Foto <span class="text-muted">(opsional, max 2MB)</span></label>
                                        @if($guru->foto)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $guru->foto) }}" alt="Foto Lama" class="img-thumbnail" style="max-height: 100px;">
                                                <small class="d-block text-muted">Foto saat ini</small>
                                            </div>
                                        @endif
                                        <div class="custom-file">
                                            <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="fotoInput" accept="image/*">
                                            <label class="custom-file-label" for="fotoInput">{{ $guru->foto ? 'Ganti foto...' : 'Pilih file...' }}</label>
                                        </div>
                                        <small class="form-text text-muted">Format: JPEG, PNG, GIF. Maks 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('guru.index') }}" class="btn btn-default">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show selected filename in custom file input
    $(document).ready(function() {
        $('#fotoInput').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).next('.custom-file-label').html(fileName);
            }
        });
    });
</script>
@endpush
@endsection
