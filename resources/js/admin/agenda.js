/**
 * Agenda Management Page JavaScript
 * Handles dynamic loading, filtering, and pagination
 */

class AgendaManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.initializeState();
        this.initializePage();
    }

    initializeElements() {
        // DOM Elements
        this.agendaSearch = document.getElementById('agenda_search');
        this.agendaDropdown = document.getElementById('agenda_dropdown');
        this.agendaIdInput = document.getElementById('agenda_id');
        this.filterForm = document.getElementById('filterForm');

        // Agendas containers
        this.agendasLoading = document.getElementById('agendas_loading');
        this.agendasTableContainer = document.getElementById('agendas_table_container');
        this.agendasPagination = document.getElementById('agendas_pagination');
        this.agendasEmpty = document.getElementById('agendas_empty');
        this.agendasInfo = document.getElementById('agendas_info');
        this.currentPageSpan = document.getElementById('current_page');
        this.lastPageSpan = document.getElementById('last_page');
        this.totalAgendasSpan = document.getElementById('total_agendas');

        // Dropdown elements
        this.noResults = document.getElementById('no_results');
        this.loadingMessage = document.getElementById('loading_message');
        this.loadingIndicator = document.getElementById('loading_indicator');
        this.optionsContainer = document.getElementById('agenda_options_container');
    }

    initializeState() {
        this.debounceTimer = null;
        this.currentRequest = null;
        this.agendaOptions = [];
        this.cachedResults = new Map();
        this.currentAgendaId = this.agendaIdInput?.value || '';
        this.currentAgendaName = '';
    }

    bindEvents() {
        // Prevent form submission
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                return false;
            });
        }

        // Agenda dropdown events
        if (this.agendaSearch) {
            this.agendaSearch.addEventListener('focus', () => this.handleAgendaFocus());
            this.agendaSearch.addEventListener('input', () => this.handleAgendaInput());
            this.agendaSearch.addEventListener('keydown', (e) => this.handleAgendaKeydown(e));
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#agenda_search') && !e.target.closest('#agenda_dropdown')) {
                this.hideDropdown();
            }
        });

        // Reset button event
        const resetBtn = document.querySelector('a[href*="agenda.index"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => this.handleReset(e));
        }

        // Delete agenda button events
        this.bindDeleteEvents();
    }

    initializePage() {
        this.loadAgendas();
    }

    handleAgendaFocus() {
        if (this.agendaOptions.length === 0) {
            this.loadAgendaOptions();
        } else {
            this.showAllOptions();
        }
        this.showDropdown();
    }

    handleAgendaInput() {
        const searchTerm = this.agendaSearch.value.toLowerCase().trim();

        clearTimeout(this.debounceTimer);

        if (searchTerm.length === 0) {
            this.agendaIdInput.value = '';
            if (this.agendaOptions.length > 0) {
                this.showAllOptions();
            } else {
                this.loadAgendaOptions();
            }
            return;
        }

        if (this.cachedResults.has(searchTerm)) {
            this.displayResults(this.cachedResults.get(searchTerm), searchTerm);
            return;
        }

        this.debounceTimer = setTimeout(() => {
            this.searchAgendas(searchTerm);
        }, 300);
    }

    handleAgendaKeydown(e) {
        const visibleOptions = this.optionsContainer?.querySelectorAll('.agenda-option');
        if (!visibleOptions) return;

        const activeOption = this.optionsContainer.querySelector('.agenda-option.active');
        let activeIndex = activeOption ? Array.from(visibleOptions).indexOf(activeOption) : -1;

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, visibleOptions.length - 1);
                this.setActiveOption(visibleOptions, activeIndex);
                break;
            case 'ArrowUp':
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                this.setActiveOption(visibleOptions, activeIndex);
                break;
            case 'Enter':
                e.preventDefault();
                if (activeOption && visibleOptions.length > 0) {
                    activeOption.click();
                }
                break;
            case 'Escape':
                this.hideDropdown();
                break;
        }
    }

    handleReset(e) {
        e.preventDefault();
        this.agendaSearch.value = '';
        this.agendaIdInput.value = '';
        this.currentAgendaId = '';
        this.loadAgendas();
        this.cachedResults.clear();
        this.agendaOptions = [];
    }

    async loadAgendaOptions(searchTerm = '') {
        this.showDropdownLoading(true);

        if (this.currentRequest) {
            this.currentRequest.abort();
        }

        try {
            const url = new URL(window.routes?.admin?.agendas?.search || '/admin/agendas/search', window.location.origin);
            if (searchTerm) {
                url.searchParams.append('q', searchTerm);
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success) {
                this.agendaOptions = data.agendas || [];

                if (searchTerm) {
                    this.cachedResults.set(searchTerm, this.agendaOptions);
                }

                this.displayResults(this.agendaOptions, searchTerm);
            } else {
                this.showDropdownError('Gagal memuat data agenda');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error loading agendas:', error);
                this.showDropdownError('Terjadi kesalahan saat memuat data');
            }
        } finally {
            this.showDropdownLoading(false);
        }
    }

    searchAgendas(searchTerm) {
        if (this.agendaOptions.length > 0) {
            const filtered = this.agendaOptions.filter(agenda =>
                agenda.nama_agenda.toLowerCase().includes(searchTerm) ||
                (agenda.dinas_nama && agenda.dinas_nama.toLowerCase().includes(searchTerm))
            );

            if (filtered.length > 0) {
                this.displayResults(filtered, searchTerm);
                return;
            }
        }

        this.loadAgendaOptions(searchTerm);
    }

    displayResults(results, searchTerm = '') {
        if (!this.optionsContainer) return;

        this.optionsContainer.innerHTML = '';

        if (results.length === 0) {
            this.noResults.style.display = 'block';
            return;
        }

        this.noResults.style.display = 'none';

        results.forEach(agenda => {
            const option = this.createOptionElement(agenda, searchTerm);
            this.optionsContainer.appendChild(option);
        });

        this.attachOptionEvents();
    }

    createOptionElement(agenda, searchTerm = '') {
        const option = document.createElement('a');
        option.href = '#';
        option.className = 'dropdown-item agenda-option';
        option.setAttribute('data-id', agenda.id);
        option.setAttribute('data-name', agenda.nama_agenda);

        let displayText = agenda.nama_agenda;
        let subtitleText = agenda.dinas_nama || 'N/A';

        if (searchTerm && searchTerm.length > 0) {
            const regex = new RegExp('(' + this.escapeRegex(searchTerm) + ')', 'gi');
            displayText = displayText.replace(regex, '<mark class="bg-warning">$1</mark>');
            subtitleText = subtitleText.replace(regex, '<mark class="bg-warning">$1</mark>');
        }

        option.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="font-weight-medium">${displayText}</div>
                    <small class="text-muted">
                        <i class="fas fa-building me-1"></i>${subtitleText}
                        ${agenda.tanggal_mulai ? ' • <i class="fas fa-calendar-alt me-1"></i>' + new Date(agenda.tanggal_mulai).toLocaleDateString('id-ID') : ''}
                    </small>
                </div>
                ${agenda.status ? `<span class="badge badge-${agenda.status === 'active' ? 'success' : 'secondary'} badge-sm">${agenda.status}</span>` : ''}
            </div>
        `;

        return option;
    }

    attachOptionEvents() {
        const options = this.optionsContainer?.querySelectorAll('.agenda-option');
        if (!options) return;

        options.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();

                const agendaId = option.getAttribute('data-id');
                const agendaName = option.getAttribute('data-name');

                // Set form values
                this.agendaSearch.value = agendaName;
                this.agendaIdInput.value = agendaId;
                this.currentAgendaId = agendaId;

                // Hide dropdown
                this.hideDropdown();

                // Remove active class from all options
                options.forEach(opt => opt.classList.remove('active'));

                // Add active class to selected option
                option.classList.add('active');

                // Load agendas for selected agenda
                this.loadAgendas(agendaId);
            });
        });
    }

    showAllOptions() {
        if (this.agendaOptions.length > 0) {
            this.displayResults(this.agendaOptions);
        } else {
            this.loadAgendaOptions();
        }
    }

    showDropdown() {
        if (this.agendaDropdown) {
            this.agendaDropdown.style.display = 'block';
        }
    }

    hideDropdown() {
        if (this.agendaDropdown) {
            this.agendaDropdown.style.display = 'none';
        }
    }

    showDropdownLoading(show) {
        if (show) {
            this.loadingMessage.style.display = 'block';
            this.loadingIndicator.style.display = 'block';
            this.noResults.style.display = 'none';
            this.showDropdown();
        } else {
            this.loadingMessage.style.display = 'none';
            this.loadingIndicator.style.display = 'none';
        }
    }

    showDropdownError(message) {
        if (this.optionsContainer) {
            this.optionsContainer.innerHTML = `
                <div class="dropdown-item text-center text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
            this.noResults.style.display = 'none';
            this.loadingMessage.style.display = 'none';
        }
    }

    setActiveOption(options, index) {
        options.forEach(opt => opt.classList.remove('active'));

        if (options[index]) {
            options[index].classList.add('active');
            options[index].scrollIntoView({ block: 'nearest' });
        }
    }

    async loadAgendas(agendaId = '') {
        this.showAgendasLoading(true);

        try {
            const url = new URL(window.routes?.admin?.agendas?.load || '/admin/agendas/load', window.location.origin);
            if (agendaId) {
                url.searchParams.append('agenda_id', agendaId);
            }

            // Add other filter parameters if needed
            if (this.filterForm) {
                const formData = new FormData(this.filterForm);
                for (let [key, value] of formData.entries()) {
                    if (value && key !== 'agenda_id') {
                        url.searchParams.append(key, value);
                    }
                }
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success) {
                this.displayAgendas(data);
            } else {
                this.showAgendasError('Gagal memuat data agenda');
            }
        } catch (error) {
            console.error('Error loading agendas:', error);
            this.showAgendasError('Terjadi kesalahan saat memuat data agenda');
        } finally {
            this.showAgendasLoading(false);
        }
    }

    displayAgendas(data) {
        // Hide all containers
        this.hideAllAgendaContainers();

        if (data.agendas.length === 0) {
            this.agendasEmpty.style.display = 'block';
        } else {
            this.agendasTableContainer.innerHTML = this.generateTableHtml(data.agendas);
            this.agendasTableContainer.style.display = 'block';

            // Update pagination
            if (data.pagination.has_pages) {
                this.agendasPagination.innerHTML = this.generatePagination(data.pagination);
                this.agendasPagination.style.display = 'block';
                // Bind events to pagination links
                this.bindPaginationEvents();
            }

            // Update info
            if (this.currentPageSpan && this.lastPageSpan && this.totalAgendasSpan) {
                this.currentPageSpan.textContent = data.pagination.current_page;
                this.lastPageSpan.textContent = data.pagination.last_page;
                this.totalAgendasSpan.textContent = data.pagination.total;
                this.agendasInfo.style.display = 'block';
            }
        }
    }

    generateTableHtml(agendas) {
        return `
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Agenda</th>
                            <th>Instansi</th>
                            <th>Tanggal</th>
                            <th>Koordinator</th>
                            <th>Link Acara</th>
                            <th>Peserta</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${agendas.map(agenda => `
                            <tr>
                                <td>${agenda.id}</td>
                                <td><strong>${agenda.nama_agenda}</strong></td>
                                <td>
                                    <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 150px;">
                                        ${agenda.dinas_nama}
                                    </span>
                                </td>
                                <td>${agenda.formatted_date}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        ${agenda.koordinator_name}
                                    </span>
                                </td>
                                <td>
                                    ${agenda.link_active ?
                                        `<a href="/admin/agenda/${agenda.id}/qrcode" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-qrcode"></i> QR Code
                                        </a>` :
                                        '<span class="text-muted">Tidak Aktif</span>'
                                    }
                                </td>
                                <td>
                                    <span class="badge badge-success">${agenda.participant_count} Peserta</span>
                                </td>
                                <td>${agenda.actions}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    generatePagination(pagination) {
        let paginationHtml = '<nav aria-label="Agenda pagination"><ul class="pagination">';

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
        if (!this.agendasPagination) return;
        const links = this.agendasPagination.querySelectorAll('a.page-link[data-page]');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute('data-page'), 10);
                if (!Number.isNaN(page)) {
                    this.loadAgendasPage(page);
                }
            });
        });
    }

    hideAllAgendaContainers() {
        if (this.agendasTableContainer) this.agendasTableContainer.style.display = 'none';
        if (this.agendasPagination) this.agendasPagination.style.display = 'none';
        if (this.agendasEmpty) this.agendasEmpty.style.display = 'none';
        if (this.agendasInfo) this.agendasInfo.style.display = 'none';
    }

    showAgendasLoading(show) {
        if (show) {
            if (this.agendasLoading) this.agendasLoading.style.display = 'block';
            this.hideAllAgendaContainers();
        } else {
            if (this.agendasLoading) this.agendasLoading.style.display = 'none';
        }
    }

    showAgendasError(message) {
        if (this.agendasTableContainer) {
            this.agendasTableContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
            this.agendasTableContainer.style.display = 'block';
        }
    }

    bindDeleteEvents() {
        // Use event delegation for dynamically loaded content
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-agenda-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-agenda-btn');
                this.handleDelete(button);
            }
        });
    }

    async handleDelete(button) {
        const route = button.getAttribute('data-route');
        const confirmMessage = button.getAttribute('data-confirm-message');
        const successMessage = button.getAttribute('data-success-message');
        const errorMessage = button.getAttribute('data-error-message');

        // Show confirmation dialog
        if (!confirm(confirmMessage)) {
            return;
        }

        // Show loading state
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(route, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Show success message
                this.showToast(successMessage, 'success');
                // Reload agendas
                this.loadAgendas();
            } else {
                throw new Error(data.message || errorMessage);
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showToast(error.message || errorMessage, 'error');
        } finally {
            // Hide loading state
            this.setButtonLoading(button, false);
        }
    }

    setButtonLoading(button, loading) {
        if (loading) {
            window.showButtonLoading(button, 'Menghapus...');
        } else {
            window.hideButtonLoading(button);
        }
    }

    showToast(message, type = 'info') {
        // Use new notification system
        if (type === 'success') {
            window.showSuccess(message);
        } else if (type === 'error') {
            window.showError(message);
        } else {
            window.showInfo(message);
        }
    }

    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}

// Global function to load agendas page (called from generated pagination)
window.loadAgendasPage = function(page) {
    if (window.agendaManager) {
        window.agendaManager.loadAgendasPage(page);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.agendaManager = new AgendaManager();
});
