@extends('admin.layouts.app')

@section('title', 'Buat Agenda Baru')

@push('styles')
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .choices__inner {
        min-height: 38px;
        padding: 5px 7.5px 3.75px;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        background-color: #fff;
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #4e73df;
    }
    .is-invalid + .choices .choices__inner {
        border-color: #e74a3b;
    }
</style>
@endpush

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Buat Agenda Baru</h1>
    <a href="{{ route('admin.agenda.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar Agenda
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Buat Agenda</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.agenda.store') }}" method="POST" id="agendaForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_agenda">Nama Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_agenda') is-invalid @enderror" 
                               id="nama_agenda" name="nama_agenda" value="{{ old('nama_agenda') }}" required>
                        @error('nama_agenda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dinas_id">Instansi <span class="text-danger">*</span></label>
                        <select class="form-select @error('dinas_id') is-invalid @enderror"
                                id="dinas_id" name="dinas_id" required>
                            @foreach($dinas as $dina)
                                <option value="{{ $dina->dinas_id }}"
                                        {{ old('dinas_id') == $dina->dinas_id ? 'selected' : '' }}>
                                    {{ $dina->nama_dinas }}
                                </option>
                            @endforeach
                        </select>
                        @error('dinas_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_agenda">Tanggal Agenda <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_agenda') is-invalid @enderror"
                               id="tanggal_agenda" name="tanggal_agenda" value="{{ old('tanggal_agenda') }}" required>
                        @error('tanggal_agenda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tempat">Tempat Acara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                               id="tempat" name="tempat" value="{{ old('tempat') }}" required>
                        @error('tempat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="waktu">Waktu Acara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('waktu') is-invalid @enderror"
                               id="waktu" name="waktu" value="{{ old('waktu') }}" placeholder="Contoh: 08.30 WIB - Selesai" required>
                        @error('waktu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="link_acara">Link Acara (opsional)</label>
                        <input type="url" class="form-control @error('link_acara') is-invalid @enderror"
                               id="link_acara" name="link_acara" value="{{ old('link_acara') }}" 
                               placeholder="https://contoh.com">
                        @error('link_acara')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="link_active" name="link_active" value="1" {{ old('link_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="link_active">
                        Aktifkan Link & QR Code
                    </label>
                </div>
            </div>
            
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Agenda
                </button>
                <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const element = document.getElementById('dinas_id');
        
        if (element) {
            const choices = new Choices(element, {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik untuk mencari...',
                itemSelectText: 'Klik untuk memilih',
                noResultsText: 'Tidak ada hasil ditemukan',
                noChoicesText: 'Tidak ada pilihan tersedia',
                removeItemButton: true,
                placeholder: true,
                placeholderValue: '-- Pilih Instansi --',
                shouldSort: false
            });

            console.log('Choices.js initialized successfully!');
        } else {
            console.error('Element #dinas_id not found');
        }
    });
</script>
@endpush