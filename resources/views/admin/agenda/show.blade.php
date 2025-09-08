@extends('admin.layouts.app')

@section('title', 'Detail Agenda')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Agenda</h1>
    <div>
        <a href="{{ route('admin.agenda.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>
</div>

<!-- Detail Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold text-white">Informasi Agenda</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Nama Agenda</h5>
                        <p class="text-muted">{{ $agenda->nama_agenda }}</p>
                        
                        <h5 class="font-weight-bold">Dinas</h5>
                        <p class="text-muted">
                            <span class="badge badge-info">
                                {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                            </span>
                        </p>
                        
                        <h5 class="font-weight-bold">Tanggal Agenda</h5>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</p>
                        
                        <h5 class="font-weight-bold">Link Acara</h5>
                        <p class="text-muted">
                            @if(!empty($agenda->unique_token))
                                <a href="{{ route('agenda.public.register.token', ['token' => $agenda->unique_token]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Halaman Pendaftaran Publik
                                </a>
                            @else
                                <span class="text-muted">Token belum tersedia</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Koordinator</h5>
                        <p class="text-muted">{{ $agenda->nama_koordinator }}</p>
                        
                        <h5 class="font-weight-bold">Total Peserta</h5>
                        <p class="text-muted">
                            <span class="badge badge-success">
                                {{ $agenda->agendaDetail->count() }} Peserta
                            </span>
                        </p>
                        
                        <h5 class="font-weight-bold">Tanggal Dibuat</h5>
                        <p class="text-muted">{{ $agenda->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold text-white">Daftar Peserta ({{ $agenda->agendaDetail->count() }})</h6>
            </div>
            <div class="card-body">
                @if($agenda->agendaDetail->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Dinas</th>
                                    <th>No HP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agenda->agendaDetail as $index => $participant)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $participant->nama }}</td>
                                        <td>{{ $participant->jabatan }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $participant->masterDinas->nama_dinas ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $participant->no_hp }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Belum ada peserta yang terdaftar untuk agenda ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold text-white">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.agenda.edit', $agenda) }}" class="btn btn-warning btn-block py-2">
                        <i class="fas fa-edit me-2"></i>Edit Agenda
                    </a>
                    
                    <a href="{{ route('admin.participants.index') }}?agenda_id={{ $agenda->id }}" class="btn btn-info btn-block py-2">
                        <i class="fas fa-users me-2"></i>Lihat Semua Peserta
                    </a>
                    
                    <a href="{{ route('admin.agenda.qrcode', $agenda) }}" class="btn btn-success btn-block py-2">
                        <i class="fas fa-qrcode me-2"></i>Generate QR Code
                    </a>
                    
                    <form action="{{ route('admin.agenda.destroy', $agenda) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block py-2">
                            <i class="fas fa-trash me-2"></i>Hapus Agenda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
