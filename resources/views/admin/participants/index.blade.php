@extends('admin.layouts.app')

@section('title', 'Manajemen Peserta')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Peserta</h1>
    <div>
        <a href="{{ route('participants.export-excel', request()->all()) }}" class="btn btn-success">Ekspor Excel</a>
        <a href="{{ route('participants.export-pdf', request()->all()) }}" class="btn btn-danger">Ekspor PDF</a>
    </div>
</div>

<!-- Filter Tanggal, Bulan, Tahun -->
<form method="GET" action="{{ route('admin.participants.index') }}" class="mb-4">
    <div class="input-group">
        <input type="number" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal" min="1" max="31" value="{{ request('tanggal') }}">
        <input type="number" name="bulan" id="bulan" class="form-control" placeholder="Bulan" min="1" max="12" value="{{ request('bulan') }}">
        <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Tahun" min="2000" max="{{ date('Y') }}" value="{{ request('tahun') }}">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(request()->filled('agenda_id'))
    @php
        $agenda = \App\Models\Agenda::find(request()->agenda_id);
    @endphp
    @if($agenda)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-filter me-2"></i>Menampilkan peserta untuk agenda: <strong>{{ $agenda->nama_agenda }}</strong>
            <a href="{{ route('admin.participants.index') }}" class="btn btn-sm btn-outline-info ml-3">
                <i class="fas fa-times me-1"></i>Hapus Filter
            </a>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endif

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Peserta</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Dinas</th>
                        <th>No HP</th>
                        <th>Agenda</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $index => $participant)
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
                            <td>
                                <span class="badge badge-primary">
                                    {{ $participant->agenda->nama_agenda ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $participant->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.participants.destroy', $participant) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>Belum ada peserta yang terdaftar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($participants->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $participants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
