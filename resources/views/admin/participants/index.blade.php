@extends('admin.layouts.app')

@section('title', 'Manajemen Peserta')

@vite(['resources/css/admin/participants.css'])

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Peserta</h1>
</div>

<!-- Filter Tanggal, Bulan, Tahun -->
<<<<<<< HEAD
<form method="GET" action="{{ route('admin.participants.index') }}" class="mb-4" id="filterForm">
    <div class="input-group">
        
        <!-- Searchable Dropdown untuk Agenda -->
        <div class="form-control p-0 position-relative" style="min-width: 200px;">
            <input type="hidden" name="agenda_id" id="agenda_id" value="{{ request('agenda_id') }}">
            <input type="text" id="agenda_search" class="form-control border-0" placeholder="Cari atau pilih agenda..." 
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
=======
<form method="GET" action="{{ route('admin.participants.index') }}" class="mb-4" id="filter-form">
    <div class="input-group">        
        <!-- Searchable Dropdown untuk Agenda -->
        <div class="form-control p-0 position-relative" style="min-width: 200px;">
            <input type="hidden" name="agenda_id" id="agenda_id" value="{{ request('agenda_id') }}">
            <input type="text" id="agenda_search" class="form-control border-0 filter-input" placeholder="Cari atau pilih agenda..." 
                   value="{{ request('agenda_id') ? $agendas->firstWhere('id', request('agenda_id'))->nama_agenda ?? '' : '' }}"
                   autocomplete="off">
            
            <!-- Dropdown Results -->
            <div id="agenda_dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto; display: none; position: absolute; top: 100%; left: 0; z-index: 1000;">
                <div class="dropdown-item text-center text-muted" id="no_results" style="display: none;">
                    <i class="fas fa-search"></i> Tidak ada hasil ditemukan
                </div>
                @foreach($agendas as $agenda)
                    <a href="#" class="dropdown-item agenda-option" 
                       data-id="{{ $agenda->id }}" 
                       data-name="{{ $agenda->nama_agenda }}">
                        {{ $agenda->nama_agenda }}
                    </a>
                @endforeach
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
            </div>
        </div>
        
        <div class="input-group-append">
<<<<<<< HEAD
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">
=======
            <button type="submit" class="btn btn-primary" id="manual-filter">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary" id="reset-filter">
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
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

<<<<<<< HEAD

=======
<div id="filter-info-container">
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
</div>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-white">Daftar Peserta</h6>
<<<<<<< HEAD
                @if(request('agenda_id'))
                    <small class="text-white-50">
                        Difilter berdasarkan agenda: {{ $agendas->firstWhere('id', request('agenda_id'))->nama_agenda ?? 'Tidak ditemukan' }}
                    </small>
                @else
                    <small class="text-white-50" id="participants_info" style="display: none;">
                        Halaman <span id="current_page">1</span> dari <span id="last_page">1</span>
                        (Total: <span id="total_participants">0</span> data)
                    </small>
                @endif
=======
                <small class="text-white-50" id="pagination-info">
                    @if($participants->hasPages())
                        Halaman {{ $participants->currentPage() }} dari {{ $participants->lastPage() }}
                        (Total: {{ $participants->total() }} data)
                    @else
                        Total: {{ $participants->count() }} data
                    @endif
                </small>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
            </div>
            <div>
                <a href="{{ route('admin.participants.create') }}" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-plus me-1"></i>Tambah Peserta
                </a>
<<<<<<< HEAD
                @if(request('agenda_id'))
                    <a href="{{ route('admin.participants.export-pdf', ['agenda_id' => request('agenda_id')]) }}" class="btn btn-light btn-sm" id="export_pdf_btn">
                        <i class="fas fa-file-pdf me-1"></i>Ekspor PDF
                    </a>
                @else
                    <a href="{{ route('admin.participants.export-pdf') }}" class="btn btn-light btn-sm" id="export_pdf_btn">
                        <i class="fas fa-file-pdf me-1"></i>Ekspor PDF
                    </a>
                @endif
=======
                <a href="{{ route('admin.participants.export-pdf', request()->all()) }}" class="btn btn-light btn-sm" id="export-pdf">
                    <i class="fas fa-file-pdf me-1"></i>Ekspor PDF
                </a>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
            </div>
        </div>
    </div>
    <div class="card-body">
<<<<<<< HEAD
        @if(request('agenda_id'))
            <!-- Server-side rendered table for filtered view -->
            @if($participants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Instansi</th>
                                <th>Agenda</th>
                                <th>No HP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $index => $participant)
                                <tr>
                                    <td>{{ $participants->firstItem() + $index }}</td>
                                    <td>{{ $participant->nama }}</td>
                                    <td>{{ $participant->jabatan }}</td>
                                    <td>
                                        <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                            {{ $participant->masterDinas->nama_dinas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 200px;">
                                            {{ $participant->agenda->nama_agenda ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $participant->no_hp }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-participant-btn"
                                                    data-route="{{ route('admin.participants.destroy', $participant) }}"
                                                    data-confirm-message="Yakin ingin menghapus peserta ini?"
                                                    data-success-message="Peserta berhasil dihapus!"
                                                    data-error-message="Gagal menghapus peserta. Silakan coba lagi."
                                                    data-reload-page="true">
                                                <i class="fas fa-trash"></i>
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination for server-side -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $participants->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>Belum ada peserta yang terdaftar untuk agenda ini</p>
                </div>
            @endif
        @else
            <!-- Loading indicator for participants -->
            <div id="participants_loading" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data peserta...</p>
            </div>

            <!-- Participants table container -->
            <div id="participants_table_container">
                <!-- Table will be loaded dynamically -->
            </div>

            <!-- Pagination container -->
            <div id="participants_pagination" class="d-flex justify-content-center mt-4" style="display: none;">
                <!-- Pagination will be loaded dynamically -->
            </div>

            <!-- Empty state -->
            <div id="participants_empty" class="text-center text-muted py-4" style="display: none;">
                <i class="fas fa-users fa-3x mb-3"></i>
                <p>Belum ada peserta yang terdaftar</p>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript Routes for AJAX calls -->
<script>
window.routes = {
    admin: {
        agendas: {
            search: '{{ route("admin.agendas.search") }}'
        },
        participants: {
            load: '{{ route("admin.participants.load") }}'
        }
    },
    participants: {
        load: '{{ route("admin.participants.load") }}',
        exportPdf: '{{ route("admin.participants.export-pdf") }}'
    }
};
</script>

@vite(['resources/js/admin/participants.js'])

@endsection
=======
        <!-- Loading Spinner -->
        <div id="loading-spinner" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Memuat data peserta...</p>
        </div>

        <div class="table-responsive" id="table-container">
            <table class="table table-bordered" id="participants-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>No HP</th>
                        <th>Dinas</th>
                        <th>Agenda</th>
                        <th>Waktu Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="participants-tbody">
                    @forelse($participants as $participant)
                        <tr>
                            <td>{{ $participant->nama }}</td>
                            <td>{{ $participant->jabatan }}</td>
                            <td>{{ $participant->no_hp ?? '-' }}</td>
                            <td>{{ $participant->masterDinas->nama_dinas ?? '-' }}</td>
                            <td>{{ $participant->agenda->nama_agenda ?? '-' }}</td>
                            <td>{{ $participant->created_at->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.participants.destroy', $participant) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data peserta ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4" id="pagination-container">
            @if($participants->hasPages())
                {{ $participants->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
