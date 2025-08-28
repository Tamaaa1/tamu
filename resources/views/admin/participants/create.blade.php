@extends('admin.layouts.app')

@section('title', 'Tambah Peserta')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Peserta</h1>
    <a href="{{ route('admin.participants.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Peserta</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.participants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="agenda_id" class="form-label">Agenda <span class="text-danger">*</span></label>
                    <select class="form-select @error('agenda_id') is-invalid @enderror" id="agenda_id" name="agenda_id" required>
                        <option value="">Pilih Agenda</option>
                        @foreach($agendas as $agenda)
                            <option value="{{ $agenda->id }}" {{ old('agenda_id') == $agenda->id ? 'selected' : '' }}>
                                {{ $agenda->nama_agenda }} ({{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('agenda_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="dinas_id" class="form-label">Dinas <span class="text-danger">*</span></label>
                    <select class="form-select @error('dinas_id') is-invalid @enderror" id="dinas_id" name="dinas_id" required>
                        <option value="">Pilih Dinas</option>
                        @foreach($dinas as $dinasItem)
                            <option value="{{ $dinasItem->dinas_id }}" {{ old('dinas_id') == $dinasItem->dinas_id ? 'selected' : '' }}>
                                {{ $dinasItem->nama_dinas }}
                            </option>
                        @endforeach
                    </select>
                    @error('dinas_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
                    @error('jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No HP <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Signature Pad Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <label class="form-label">Tanda Tangan</label>
                    <div class="card">
                        <div class="card-body">
                            <div class="signature-pad-container">
                                <canvas id="signature-pad" class="signature-pad" width="400" height="200" style="border: 1px solid #ddd; background: white;"></canvas>
                            </div>
                            <div class="mt-3">
                                <button type="button" id="clear-signature" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser me-1"></i>Hapus
                                </button>
                                <button type="button" id="save-signature" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan Tanda Tangan
                                </button>
                            </div>
                            <input type="hidden" id="signature-data" name="gambar_ttd">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Include Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tunggu hingga library SignaturePad siap
        if (typeof SignaturePad === 'undefined') {
            console.error('SignaturePad library not loaded');
            return;
        }

        const canvas = document.getElementById('signature-pad');
        if (!canvas) {
            console.error('Canvas element not found');
            return;
        }

        const signaturePad = new SignaturePad(canvas);
        const clearButton = document.getElementById('clear-signature');
        const saveButton = document.getElementById('save-signature');
        const signatureData = document.getElementById('signature-data');
        
        if (!signaturePad || !clearButton || !saveButton || !signatureData) {
            console.error('Required elements not found');
            return;
        }

        // Adjust canvas coordinate space taking into account pixel ratio
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            
            // Clear the canvas after resize
            if (signaturePad && typeof signaturePad.clear === 'function') {
                signaturePad.clear();
            }
        }
        
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        
        clearButton.addEventListener('click', function() {
            if (signaturePad && typeof signaturePad.clear === 'function') {
                signaturePad.clear();
                signatureData.value = '';
            }
        });
        
        saveButton.addEventListener('click', function() {
            if (!signaturePad || typeof signaturePad.isEmpty !== 'function') {
                alert('Signature pad tidak siap. Silakan refresh halaman.');
                return;
            }
            
            if (signaturePad.isEmpty()) {
                alert('Silakan buat tanda tangan terlebih dahulu.');
            } else {
                if (typeof signaturePad.toDataURL === 'function') {
                    const dataURL = signaturePad.toDataURL('image/png');
                    signatureData.value = dataURL;
                    alert('Tanda tangan berhasil disimpan!');
                } else {
                    alert('Gagal menyimpan tanda tangan.');
                }
            }
        });
    });
</script>

<style>
    .signature-pad-container {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .signature-pad {
        width: 100%;
        height: 200px;
        cursor: crosshair;
    }
</style>
@endsection
