@extends('admin.layouts.app')

@section('title', 'Edit Peserta')
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
    <h1 class="h3 mb-0 text-gray-800">Edit Peserta</h1>
    <a href="{{ route('admin.participants.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Edit Peserta</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.participants.update', $participant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="agenda_id" class="form-label">Agenda <span class="text-danger">*</span></label>
                    <select class="form-select @error('agenda_id') is-invalid @enderror" id="agenda_id" name="agenda_id" required>
                        <option value="">Pilih Agenda</option>
                        @foreach($agendas as $agenda)
                            <option value="{{ $agenda->id }}" {{ old('agenda_id', $participant->agenda_id) == $agenda->id ? 'selected' : '' }}>
                                {{ $agenda->nama_agenda }} ({{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('agenda_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $participant->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $participant->jabatan) }}" required>
                    @error('jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('gender', $participant->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $participant->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label">No HP <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $participant->no_hp) }}" required>
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if($participant->gambar_ttd)
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">Preview Tanda Tangan</label>
                    <div>
                        @if(strpos($participant->gambar_ttd, 'data:image/') === 0)
                            <img src="{{ $participant->gambar_ttd }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        @else
                            @php
                                $filename = basename($participant->gambar_ttd);
                            @endphp
                            <img src="{{ route('admin.signature.serve', $filename) }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
                <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">
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
