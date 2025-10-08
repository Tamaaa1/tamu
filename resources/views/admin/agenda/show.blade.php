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
                        
                        <h5 class="font-weight-bold">Instansi</h5>
                        <p class="text-muted">
                            <span class="badge badge-info" style="word-wrap: break-word; white-space: normal; max-width: 300px;">
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
                                {{ $participants->total() }} Peserta
                            </span>
                        </p>
                        
                        <h5 class="font-weight-bold">Tanggal Dibuat</h5>
                        <p class="text-muted">{{ $agenda->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
<<<<<<< HEAD
<!-- Participants Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold text-white">Daftar Peserta (<span id="total_participants">{{ $participants->total() }}</span>)</h6>
    </div>
    <div class="card-body">
        <div id="participants_loading" style="display:none;" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
=======
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
                                                <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 300px;">
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
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
            </div>
            <p>Memuat data peserta...</p>
        </div>

        <div id="participants_empty" style="display:none;" class="text-center text-muted py-4">
            <i class="fas fa-users fa-3x mb-3"></i>
            <p>Belum ada peserta yang terdaftar untuk agenda ini</p>
        </div>

        <div id="participants_table_container" style="display:none;">
            <!-- Participants table will be loaded here by AJAX -->
        </div>

        <div id="participants_pagination" class="d-flex justify-content-center mt-3" style="display:none;">
            <!-- Pagination links will be loaded here by AJAX -->
        </div>
    </div>
</div>

<script>
class AgendaParticipantsLoader {
    constructor(agendaId) {
        this.agendaId = agendaId;
        this.loadingElement = document.getElementById('participants_loading');
        this.tableContainer = document.getElementById('participants_table_container');
        this.paginationContainer = document.getElementById('participants_pagination');
        this.emptyElement = document.getElementById('participants_empty');
        this.totalElement = document.getElementById('total_participants');
        this.currentPage = 1;
    }

    async loadParticipants(page = 1) {
        this.showLoading(true);

        try {
            const url = new URL('/admin/participants/load', window.location.origin);
            url.searchParams.append('agenda_id', this.agendaId);
            url.searchParams.append('page', page);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.displayParticipants(data);
            } else {
                this.showError(data.message || 'Gagal memuat data peserta');
            }
        } catch (error) {
            console.error('Error loading participants:', error);
            this.showError('Terjadi kesalahan saat memuat data peserta');
        } finally {
            this.showLoading(false);
        }
    }

    displayParticipants(data) {
        // Hide all containers
        this.hideAllContainers();

        if (data.total === 0) {
            this.showEmptyState();
        } else {
            this.showParticipantsTable(data);
            this.showPagination(data);
        }

        // Update total count
        if (this.totalElement) {
            this.totalElement.textContent = data.total;
        }
    }

    hideAllContainers() {
        if (this.tableContainer) this.tableContainer.style.display = 'none';
        if (this.paginationContainer) this.paginationContainer.style.display = 'none';
        if (this.emptyElement) this.emptyElement.style.display = 'none';
    }

    showEmptyState() {
        if (this.emptyElement) {
            this.emptyElement.style.display = 'block';
        }
    }

    showParticipantsTable(data) {
        if (this.tableContainer && data.html) {
            this.tableContainer.innerHTML = data.html;
            this.tableContainer.style.display = 'block';
        }
    }

    showPagination(data) {
        if (this.paginationContainer && data.pagination?.has_pages) {
            this.paginationContainer.innerHTML = this.generatePagination(data.pagination);
            this.paginationContainer.style.display = 'block';
            this.bindPaginationEvents();
        }
    }

    generatePagination(pagination) {
        let paginationHtml = '<nav aria-label="Participants pagination"><ul class="pagination justify-content-center">';

        // Previous button
        if (pagination.current_page > 1) {
            paginationHtml += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>`;
        }

        // Page numbers
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            if (i === pagination.current_page) {
                paginationHtml += `<li class="page-item active">
                    <span class="page-link">${i}</span>
                </li>`;
            } else {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>`;
        }

        paginationHtml += '</ul></nav>';
        return paginationHtml;
    }

    bindPaginationEvents() {
        if (!this.paginationContainer) return;
        const links = this.paginationContainer.querySelectorAll('a.page-link[data-page]');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute('data-page'), 10);
                if (!isNaN(page)) {
                    this.loadParticipants(page);
                }
            });
        });
    }

    showLoading(show) {
        if (show) {
            this.hideAllContainers();
            if (this.loadingElement) this.loadingElement.style.display = 'block';
        } else {
            if (this.loadingElement) this.loadingElement.style.display = 'none';
        }
    }

    showError(message) {
        if (this.tableContainer) {
            this.tableContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
            this.tableContainer.style.display = 'block';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const agendaId = {{ $agenda->id }};
    const loader = new AgendaParticipantsLoader(agendaId);
    loader.loadParticipants();
});
</script>
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
