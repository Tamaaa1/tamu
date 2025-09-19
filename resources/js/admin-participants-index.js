document.addEventListener('DOMContentLoaded', function() {
    let filterTimeout;
    const loadingSpinner = document.getElementById('loading-spinner');
    const tableContainer = document.getElementById('table-container');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationInfo = document.getElementById('pagination-info');
    const filterInfoContainer = document.getElementById('filter-info-container');
    const exportPdfBtn = document.getElementById('export-pdf');

    // Enhanced loading indicators
    function showLoading() {
        loadingSpinner.style.display = 'block';
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';

        document.querySelectorAll('.filter-input, #manual-filter, #reset-filter').forEach(el => {
            el.disabled = true;
        });
    }

    // Enhanced hide loading
    function hideLoading() {
        loadingSpinner.style.display = 'none';
        tableContainer.style.opacity = '1';
        tableContainer.style.pointerEvents = 'auto';

        document.querySelectorAll('.filter-input, #manual-filter, #reset-filter').forEach(el => {
            el.disabled = false;
        });
    }

    // Update export PDF link
    function updateExportLink() {
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }

        const baseUrl = '{{ route("admin.participants.export-pdf") }}';
        exportPdfBtn.href = baseUrl + '?' + params.toString();
    }

    // Load participants via AJAX
    function loadParticipants() {
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }

        showLoading();

        fetch('{{ route("admin.participants.data") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTableContent(data.data);
                updatePaginationInfo(data.pagination);
                updateFilterInfo(data.filters);
                updateExportLink();

                const newUrl = '{{ route("admin.participants.index") }}' +
                              (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('participants-tbody').innerHTML =
                '<tr><td colspan="8" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>';
        })
        .finally(() => {
            hideLoading();
        });
    }

    // Update table content
    function updateTableContent(participants) {
        const tbody = document.getElementById('participants-tbody');

        if (participants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data peserta ditemukan</td></tr>';
            return;
        }

        let html = '';
        participants.forEach(participant => {
            html += `
                <tr>
                    <td>${participant.nama}</td>
                    <td>${participant.jabatan}</td>
                    <td>${participant.no_hp}</td>
                    <td>${participant.gender}</td>
                    <td>${participant.dinas}</td>
                    <td>${participant.agenda}</td>
                    <td>${participant.created_at}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="/admin/participants/${participant.id}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/participants/${participant.id}/edit" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/admin/participants/${participant.id}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Update pagination info
    function updatePaginationInfo(pagination) {
        if (pagination.has_pages) {
            paginationInfo.innerHTML = `
                Halaman ${pagination.current_page} dari ${pagination.last_page}
                (Total: ${pagination.total} data)
            `;
        } else {
            paginationInfo.innerHTML = `Total: ${pagination.total} data`;
        }

        if (pagination.has_pages && pagination.links) {
            paginationContainer.innerHTML = pagination.links;
        } else {
            paginationContainer.innerHTML = '';
        }
    }

    // Update filter info
    function updateFilterInfo(filters) {
        if (filters.agenda_id) {
            const agendaOption = document.querySelector(`.agenda-option[data-id="${filters.agenda_id}"]`);
            const agendaName = agendaOption ? agendaOption.textContent : 'Agenda Terpilih';

            filterInfoContainer.innerHTML = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-filter me-2"></i>Menampilkan peserta untuk agenda: <strong>${agendaName}</strong>
                    <a href="{{ route('admin.participants.index') }}" class="btn btn-sm btn-outline-info ml-3">
                        <i class="fas fa-times me-1"></i>Hapus Filter
                    </a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
        } else {
            filterInfoContainer.innerHTML = '';
        }
    }

    // Filter inputs
    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            const delay = this.type === 'date' ? 300 : 500;
            filterTimeout = setTimeout(() => loadParticipants(), delay);
        });

        if (input.type === 'date' || input.type === 'number') {
            input.addEventListener('change', function() {
                clearTimeout(filterTimeout);
                loadParticipants();
            });
        }
    });

    // Auto-clear date inputs
    document.getElementById('start_date').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('tanggal').value = '';
            document.getElementById('bulan').value = '';
            document.getElementById('tahun').value = '';
        }
    });

    document.getElementById('end_date').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('tanggal').value = '';
            document.getElementById('bulan').value = '';
            document.getElementById('tahun').value = '';
        }
    });

    ['tanggal', 'bulan', 'tahun'].forEach(id => {
        document.getElementById(id).addEventListener('input', function() {
            if (this.value) {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
            }
        });
    });

    // Agenda dropdown
    const agendaSearch = document.getElementById('agenda_search');
    const agendaDropdown = document.getElementById('agenda_dropdown');
    const agendaId = document.getElementById('agenda_id');
    const noResults = document.getElementById('no_results');

    function fetchAgendaResults(searchTerm) {
        fetch('{{ route("admin.participants.search-agenda") }}?q=' + encodeURIComponent(searchTerm), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(agendas => {
            document.querySelectorAll('.agenda-option').forEach(option => option.style.display = 'none');
            let hasResults = false;

            agendas.forEach(agenda => {
                const existingOption = document.querySelector(`[data-id="${agenda.id}"]`);
                if (existingOption) {
                    existingOption.style.display = 'block';
                    hasResults = true;
                }
            });

            noResults.style.display = hasResults ? 'none' : 'block';
        })
        .catch(error => console.error('Error fetching agenda results:', error));
    }

    agendaSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        if (!searchTerm) {
            agendaId.value = '';
            agendaDropdown.style.display = 'none';

            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => loadParticipants(), 300);
            return;
        }

        let hasResults = false;
        document.querySelectorAll('.agenda-option').forEach(option => {
            const name = option.textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                option.style.display = 'block';
                hasResults = true;
            } else {
                option.style.display = 'none';
            }
        });

        noResults.style.display = hasResults ? 'none' : 'block';
        agendaDropdown.style.display = 'block';

        if (searchTerm.length >= 2) {
            clearTimeout(window.agendaSearchTimeout);
            window.agendaSearchTimeout = setTimeout(() => fetchAgendaResults(searchTerm), 300);
        }
    });

    document.querySelectorAll('.agenda-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            agendaSearch.value = name;
            agendaId.value = id;
            agendaDropdown.style.display = 'none';

            clearTimeout(filterTimeout);
            loadParticipants();
        });
    });

    document.addEventListener('click', function(e) {
        if (!agendaSearch.contains(e.target) && !agendaDropdown.contains(e.target)) {
            agendaDropdown.style.display = 'none';
        }
    });

    // Reset filter
    document.getElementById('reset-filter').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.filter-input').forEach(input => input.value = '');
        agendaId.value = '';
        loadParticipants();
    });

    // Prevent form submit
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadParticipants();
    });

    // Pagination with AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.page-link')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page');

            if (page) {
                const formData = new FormData(document.getElementById('filter-form'));
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value) params.append(key, value);
                }
                params.append('page', page);

                showLoading();

                fetch('{{ route("admin.participants.data") }}?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTableContent(data.data);
                        updatePaginationInfo(data.pagination);

                        const newUrl = '{{ route("admin.participants.index") }}?' + params.toString();
                        window.history.replaceState({}, '', newUrl);

                        document.getElementById('participants-table').scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => hideLoading());
            }
        }
    });
});
