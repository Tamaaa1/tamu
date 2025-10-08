@extends('admin.layouts.app')

@section('title', 'Detail Peserta')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Peserta</h1>
    <div>
        <a href="{{ route('admin.participants.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
        <a href="{{ route('admin.participants.edit', $participant) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit
        </a>
    </div>
</div>

<!-- Detail Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Peserta</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Nama Lengkap</h5>
                        <p class="text-muted">{{ $participant->nama }}</p>
                        
                        <h5 class="font-weight-bold">Jabatan</h5>
                        <p class="text-muted">{{ $participant->jabatan }}</p>
                        
                        <h5 class="font-weight-bold">No HP</h5>
                        <p class="text-muted">{{ $participant->no_hp }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Instansi</h5>
                        <p class="text-muted">
                                <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 300px;">
                                    {{ $participant->masterDinas->nama_dinas ?? 'N/A' }}
                                </span>
                        </p>
                        
                        <h5 class="font-weight-bold">Agenda</h5>
                        <p class="text-muted">
                            <span class="badge badge-primary">
                                {{ $participant->agenda->nama_agenda ?? 'N/A' }}
                            </span>
                        </p>
                        
                        <h5 class="font-weight-bold">Tanggal Registrasi</h5>
                        <p class="text-muted">{{ $participant->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($participant->gambar_ttd)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="font-weight-bold">Tanda Tangan Digital</h5>
                        @if(strpos($participant->gambar_ttd, 'data:image/') === 0)
                            <img src="{{ $participant->gambar_ttd }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px;">
                        @else
                            @php
                                $filename = basename($participant->gambar_ttd);
                            @endphp
                            <img src="{{ route('admin.signature.serve', $filename) }}" alt="Tanda Tangan" class="img-fluid" style="max-height: 100px;">
                        @endif
                    </div>
                </div>
                @endif
                

            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit me-2"></i>Edit Peserta
                    </a>
                    
                    <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list me-2"></i>Lihat Semua Peserta
                    </a>
                    
                    <a href="{{ route('admin.agenda.show', $participant->agenda) }}" class="btn btn-info btn-block">
                        <i class="fas fa-calendar me-2"></i>Lihat Detail Agenda
                    </a>
                    
                    <form action="{{ route('admin.participants.destroy', $participant) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash me-2"></i>Hapus Peserta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
