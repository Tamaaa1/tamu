@extends('admin.layouts.app')

@section('title', 'Manajemen Agenda')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Agenda</h1>
</div>

<!-- Filter Tanggal, Bulan, Tahun -->
<form method="GET" action="{{ route('admin.agenda.index') }}" class="mb-4">
    <div class="input-group">
        <input type="number" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal" min="1" max="31" value="{{ request('tanggal') }}">
        <input type="number" name="bulan" id="bulan" class="form-control" placeholder="Bulan" min="1" max="12" value="{{ request('bulan') }}">
        <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Tahun" min="2000" max="{{ date('Y') }}" value="{{ request('tahun') }}">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Reset</a>
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

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold text-light">Daftar Agenda</h6>
            @if($agendas->hasPages())
                <small class="text-light" id="pagination-info">
                    Halaman {{ $agendas->currentPage() }} dari {{ $agendas->lastPage() }}
                    (Total: {{ $agendas->total() }} data)
                </small>
            @endif
        </div>
        <a href="{{ route('admin.agenda.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-1"></i> Buat Agenda Baru
        </a>
    </div>
    <div class="card-body">
        <!-- Loading Spinner -->
        <div id="loading-spinner" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat data agenda...</p>
        </div>
        <div class="table-responsive" id="table-container">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Agenda</th>
                        <th>Dinas</th>
                        <th>Tanggal</th>
                        <th>Koordinator</th>
                        <th>Link Acara</th>
                        <th>Peserta</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="agendas-tbody">
                    @forelse($agendas as $index => $agenda)
                        <tr>
                            <td>{{ ($agendas->currentPage() - 1) * $agendas->perPage() + $index + 1 }}</td>
                            <td>
                                <strong>{{ $agenda->nama_agenda }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 150px;">
                                    {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $agenda->koordinator->name ?? $agenda->nama_koordinator }}
                                </span>
                            </td>
                            <td class="link-column">
                                @if($agenda->link_active)
                                    <a href="{{ route('admin.agenda.qrcode', $agenda) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-qrcode"></i> QR Code
                                    </a>
                                @else
                                    <span class="text-muted">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $agenda->agendaDetail->count() }} Peserta</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.agenda.show', $agenda) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.agenda.edit', $agenda) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.agenda.toggle-link', $agenda) }}"
                                          method="POST"
                                          class="d-inline toggle-link-form"
                                          data-agenda-id="{{ $agenda->id }}">
                                        @csrf
                                        @if($agenda->link_active)
                                            <button type="submit" class="btn btn-sm btn-success" title="Nonaktifkan Link & QR Code">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Aktifkan Link & QR Code">
                                                <i class="fas fa-toggle-off"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <form action="{{ route('admin.agenda.destroy', $agenda) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
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
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <p>Belum ada agenda yang dibuat</p>
                                <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Buat Agenda Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-center" id="pagination-container">
                {{ $agendas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
