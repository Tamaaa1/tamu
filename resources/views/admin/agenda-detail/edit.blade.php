@extends('admin.layouts.app')

@section('title', 'Edit Peserta')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Peserta</h1>
    <a href="{{ route('admin.agenda-detail.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar Peserta
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Edit Peserta</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.agenda-detail.update', $agendaDetail) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="agenda_id">Agenda <span class="text-danger">*</span></label>
                        <select class="form-control @error('agenda_id') is-invalid @enderror" id="agenda_id" name="agenda_id" required>
                            <option value="">-- Pilih Agenda --</option>
                            @foreach($agendas as $agenda)
                                <option value="{{ $agenda->id }}" {{ old('agenda_id', $agendaDetail->agenda_id) == $agenda->id ? 'selected' : '' }}>
                                    {{ $agenda->nama_agenda }}
                                </option>
                            @endforeach
                        </select>
                        @error('agenda_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" name="nama" value="{{ old('nama', $agendaDetail->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                               id="jabatan" name="jabatan" value="{{ old('jabatan', $agendaDetail->jabatan) }}" required>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dinas_id">Instansi <span class="text-danger">*</span></label>
                        <select class="form-control @error('dinas_id') is-invalid @enderror" id="dinas_id" name="dinas_id" required>
                            <option value="">-- Pilih Dinas --</option>
                            @foreach($dinas as $dina)
                                <option value="{{ $dina->dinas_id }}" {{ old('dinas_id', $agendaDetail->dinas_id) == $dina->dinas_id ? 'selected' : '' }}>
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
                        <label for="no_hp">No HP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                               id="no_hp" name="no_hp" value="{{ old('no_hp', $agendaDetail->no_hp) }}" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="gambar_ttd">Tanda Tangan Digital</label>
                        <input type="file" class="form-control @error('gambar_ttd') is-invalid @enderror" 
                               id="gambar_ttd" name="gambar_ttd" accept="image/*">
                        @error('gambar_ttd')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            @if($agendaDetail->gambar_ttd)
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">Preview Tanda Tangan</label>
                    <div>
                        @if(strpos($agendaDetail->gambar_ttd, 'data:image/') === 0)
                            <img src="{{ $agendaDetail->gambar_ttd }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        @else
                            @php
                                $filename = basename($agendaDetail->gambar_ttd);
                            @endphp
                            <img src="{{ route('admin.signature.serve', $filename) }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Peserta
                </button>
                <a href="{{ route('admin.agenda-detail.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
