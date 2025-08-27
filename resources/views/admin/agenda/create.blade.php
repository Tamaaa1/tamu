@extends('admin.layouts.app')

@section('title', 'Buat Agenda Baru')

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
        <form action="{{ route('admin.agenda.store') }}" method="POST">
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
                        <label for="dinas_id">Dinas <span class="text-danger">*</span></label>
                        <select class="form-control @error('dinas_id') is-invalid @enderror" id="dinas_id" name="dinas_id" required>
                            <option value="">-- Pilih Dinas --</option>
                            @foreach($dinas as $dina)
                                <option value="{{ $dina->dinas_id }}" {{ old('dinas_id') == $dina->dinas_id ? 'selected' : '' }}>
                                    {{ $dina->nama_dinas }}
                                </option>
                            @endforeach
                        </select>
                        @error('dinas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
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
                        <label for="link_acara">Link Acara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('link_acara') is-invalid @enderror" 
                               id="link_acara" name="link_acara" value="{{ old('link_acara') }}">
                        @error('link_acara')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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
