@extends('admin.layouts.app')

@section('title', 'Manajemen Agenda')

@vite(['resources/css/admin/agenda.css'])

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Agenda</h1>
</div>

<!-- Searchable Dropdown untuk Agenda -->
<form method="GET" action="{{ route('admin.agenda.index') }}" class="mb-4" id="filterForm">
    <div class="input-group">

        <!-- Searchable Dropdown untuk Agenda -->
        <div class="form-control p-0 position-relative" style="min-width: 300px;">
            <input type="hidden" name="agenda_id" id="agenda_id" value="{{ request('agenda_id') }}">
            <input type="text" id="agenda_search" class="form-control border-0" placeholder="Cari agenda berdasarkan nama, dinas, atau koordinator..."
                   value="{{ request('agenda_id') ? $agendas->firstWhere('id', request('agenda_id'))->nama_agenda ?? '' : '' }}"
                   autocomplete="off">

            <!-- Loading indicator -->
            <div id="loading_indicator" class="position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <!-- Dropdown Results -->
            <div id="agenda_dropdown" class="dropdown-menu w-100" style="max-height: 250px; overflow-y: auto; display: none; position: absolute; top: 100%; left: 0; z-index: 1000;">
                <div class="dropdown-item text-center text-muted" id="loading_message" style="display: none;">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Memuat data agenda...
                </div>
                <div class="dropdown-item text-center text-muted" id="no_results" style="display: none;">
                    <i class="fas fa-search"></i> Tidak ada hasil ditemukan
                </div>
                <div id="agenda_options_container">
                    <!-- Options will be loaded dynamically -->
                </div>
            </div>
        </div>

        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Reset
            </a>
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
<<<<<<< HEAD
            <small class="text-white-50" id="agendas_info" style="display: none;">
                Halaman <span id="current_page">1</span> dari <span id="last_page">1</span>
                (Total: <span id="total_agendas">0</span> data)
            </small>
=======
            @if($agendas->hasPages())
                <small class="text-light" id="pagination-info">
                    Halaman {{ $agendas->currentPage() }} dari {{ $agendas->lastPage() }}
                    (Total: {{ $agendas->total() }} data)
                </small>
            @endif
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        </div>
        <a href="{{ route('admin.agenda.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-1"></i> Buat Agenda Baru
        </a>
    </div>
    <div class="card-body">
<<<<<<< HEAD
        <!-- Loading indicator for agendas -->
        <div id="agendas_loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
=======
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
            </div>
            <p class="mt-2 text-muted">Memuat data agenda...</p>
        </div>

        <!-- Agendas table container -->
        <div id="agendas_table_container">
            <!-- Table will be loaded dynamically -->
        </div>

        <!-- Pagination container -->
        <div id="agendas_pagination" class="d-flex justify-content-center mt-4" style="display: none;">
            <!-- Pagination will be loaded dynamically -->
        </div>

        <!-- Empty state -->
        <div id="agendas_empty" class="text-center text-muted py-4" style="display: none;">
            <i class="fas fa-calendar-times fa-3x mb-3"></i>
            <p>Belum ada agenda yang dibuat</p>
            <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Buat Agenda Pertama
            </a>
        </div>
    </div>
</div>

@vite(['resources/js/admin/agenda.js'])

@endsection
