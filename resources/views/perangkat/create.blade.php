@extends('layouts.adminlte')

@section('title', 'Tambah Perangkat')
@section('page-title', 'Tambah Perangkat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('perangkat.index') }}">Perangkat</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Perangkat</h3>
    </div>
    <form action="{{ route('perangkat.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Perangkat <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                       value="{{ old('nama') }}" placeholder="Contoh: Gerbang Utama, Ruang Kelas 7A" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}" 
                       placeholder="Contoh: Gerbang Depan, Lantai 2">
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipe Perangkat <span class="text-danger">*</span></label>
                        <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror" required>
                            <option value="">Pilih Tipe...</option>
                            <option value="gerbang" {{ old('tipe') == 'gerbang' ? 'selected' : '' }}>Gerbang (Pintu Masuk/Pulang)</option>
                            <option value="kelas" {{ old('tipe') == 'kelas' ? 'selected' : '' }}>Kelas (Absensi per Ruang Kelas)</option>
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <strong>Gerbang:</strong> Dipasang di pintu masuk<br>
                            <strong>Kelas:</strong> Dipasang di dalam kelas
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Vendor/Platform <span class="text-danger">*</span></label>
                        <select name="vendor_type" id="vendor_type" class="form-control @error('vendor_type') is-invalid @enderror" required>
                            <option value="">Pilih Vendor...</option>
                            @foreach($vendorTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('vendor_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('vendor_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <strong>ESP32:</strong> Custom hardware (belum ada middleware)<br>
                            <strong>Solution/ZKTeco/Hikvision:</strong> Perangkat komersial (butuh middleware)
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="form-group" id="kelasField" style="display: none;">
                <label>Pilih Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" id="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror">
                    <option value="">Pilih Kelas...</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
                @error('kelas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Pilih kelas tempat perangkat ini dipasang</small>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Device Key</strong> akan digenerate otomatis setelah simpan.
            </div>
            
            <div class="alert alert-warning" id="middlewareWarning" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Perhatian:</strong> Vendor ini membutuhkan middleware/adapter untuk koneksi ke sistem.
                Middleware akan dikembangkan terpisah di Phase 1 Adapter Stream.
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('perangkat.index') }}" class="btn btn-default">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide kelas field based on tipe
        function toggleKelasField() {
            var tipe = $('#tipe').val();
            if (tipe === 'kelas') {
                $('#kelasField').show();
                $('#kelas_id').attr('required', true);
            } else {
                $('#kelasField').hide();
                $('#kelas_id').removeAttr('required');
                $('#kelas_id').val('');
            }
        }
        
        // Show warning for commercial devices
        function toggleMiddlewareWarning() {
            var vendor = $('#vendor_type').val();
            if (vendor && vendor !== 'esp32') {
                $('#middlewareWarning').show();
            } else {
                $('#middlewareWarning').hide();
            }
        }
        
        // Initial check
        toggleKelasField();
        toggleMiddlewareWarning();
        
        // On change
        $('#tipe').on('change', function() {
            toggleKelasField();
        });
        
        $('#vendor_type').on('change', function() {
            toggleMiddlewareWarning();
        });
    });
</script>
@endpush
