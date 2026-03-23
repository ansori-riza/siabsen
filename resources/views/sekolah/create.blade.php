@extends('layouts.adminlte')

@section('title', 'Tambah Sekolah')
@section('page-title', 'Tambah Sekolah')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sekolah.index') }}">Sekolah</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Form Tambah Sekolah</h3>
            </div>
            <form action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Dasar -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Dasar</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama Sekolah/Lembaga <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                               value="{{ old('nama') }}" placeholder="Contoh: SD Negeri 1 Jakarta" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>NPSN <span class="text-muted">(opsional)</span></label>
                                        <input type="text" name="npsn" class="form-control @error('npsn') is-invalid @enderror" 
                                               value="{{ old('npsn') }}" placeholder="Nomor Pokok Sekolah Nasional">
                                        @error('npsn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Kepala Sekolah/Pengasuh <span class="text-muted">(opsional)</span></label>
                                        <input type="text" name="kepala_sekolah" class="form-control @error('kepala_sekolah') is-invalid @enderror" 
                                               value="{{ old('kepala_sekolah') }}" placeholder="Nama Kepala Sekolah">
                                        @error('kepala_sekolah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat <span class="text-muted">(opsional)</span></label>
                                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" 
                                                  placeholder="Alamat lengkap sekolah">{{ old('alamat') }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pengaturan Tampilan -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-paint-brush"></i> Pengaturan Tampilan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Tipe Lembaga <span class="text-danger">*</span></label>
                                        <select name="institution_type" class="form-control @error('institution_type') is-invalid @enderror" required>
                                            <option value="">Pilih Tipe...</option>
                                            @foreach($institutionTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('institution_type') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            Tipe lembaga menentukan terminologi yang digunakan (Guru/Murid vs Ustadz/Santri)
                                        </small>
                                        @error('institution_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Warna Tema <span class="text-muted">(opsional)</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="colorPreview" style="background-color: {{ old('theme_color', '#1971C2') }}; width: 40px; border: 1px solid #ccc;">&nbsp;</span>
                                            </div>
                                            <input type="text" name="theme_color" id="themeColor" 
                                                   class="form-control @error('theme_color') is-invalid @enderror" 
                                                   value="{{ old('theme_color', '#1971C2') }}" 
                                                   placeholder="#1971C2" maxlength="7">
                                            <div class="input-group-append">
                                                <input type="color" class="form-control" style="width: 50px; padding: 0;" 
                                                       value="{{ old('theme_color', '#1971C2') }}" 
                                                       onchange="document.getElementById('themeColor').value = this.value; document.getElementById('colorPreview').style.backgroundColor = this.value;">
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Format: #RRGGBB (contoh: #1971C2)</small>
                                        @error('theme_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Logo <span class="text-muted">(opsional, max 2MB)</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="logo" class="custom-file-input @error('logo') is-invalid @enderror" id="logoInput" accept="image/*">
                                            <label class="custom-file-label" for="logoInput">Pilih file...</label>
                                        </div>
                                        <small class="form-text text-muted">Format: JPEG, PNG, GIF. Maks 2MB.</small>
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="isActive">Status Aktif</label>
                                        </div>
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
                    <a href="{{ route('sekolah.index') }}" class="btn btn-default">
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
        $('#logoInput').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Color input sync
        $('#themeColor').on('change', function() {
            $('#colorPreview').css('background-color', $(this).val());
        });
    });
</script>
@endpush
@endsection
