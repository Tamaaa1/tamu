/**
 * Participants Management Page JavaScript
 * Handles dynamic loading, filtering, and pagination
 */

class ParticipantsManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.bindDeleteEvents();
        this.initializeState();
        this.initializePage();
    }

    initializeElements() {
        // DOM Elements
        this.agendaSearch = document.getElementById('agenda_search');
        this.agendaDropdown = document.getElementById('agenda_dropdown');
        this.agendaIdInput = document.getElementById('agenda_id');
        this.filterForm = document.getElementById('filterForm');

        // Participants containers
        this.participantsLoading = document.getElementById('participants_loading');
        this.participantsTableContainer = document.getElementById('participants_table_container');
        this.participantsPagination = document.getElementById('participants_pagination');
        this.participantsEmpty = document.getElementById('participants_empty');
        this.participantsInfo = document.getElementById('participants_info');
        this.currentPageSpan = document.getElementById('current_page');
        this.lastPageSpan = document.getElementById('last_page');
        this.totalParticipantsSpan = document.getElementById('total_participants');
        this.exportPdfBtn = document.getElementById('export_pdf_btn');

        // Dropdown elements
        this.noResults = document.getElementById('no_results');
        this.loadingMessage = document.getElementById('loading_message');
        this.loadingIndicator = document.getElementById('loading_indicator');
        this.optionsContainer = document.getElementById('agenda_options_container');

        // Debug logging
        console.log('ParticipantsManager: Elements initialized', {
            agendaSearch: !!this.agendaSearch,
            agendaDropdown: !!this.agendaDropdown,
            agendaIdInput: !!this.agendaIdInput,
            filterForm: !!this.filterForm,
            optionsContainer: !!this.optionsContainer
        });
    }

    initializeState() {
        this.debounceTimer = null;
        this.currentRequest = null;
        this.abortController = null;
        this.agendaOptions = [];
        this.cachedResults = new Map();
        this.currentAgendaId = this.agendaIdInput?.value || '';
        this.currentAgendaName = this.agendaSearch?.value.trim() || '';
        this.processingDeletes = new Set(); // Track processing delete requests
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
            this.agendaSearch.addEventListener('keydown', (e) => this.handleKeyNavigation(e));
        }

        // Global click handler for dropdown
        document.addEventListener('click', (e) => this.handleGlobalClick(e));

        // Reset button handler
        const resetBtn = document.querySelector('a[href*="participants.index"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => this.handleReset(e));
        }

        // Delete participant button events
        this.bindDeleteEvents();
    }

    initializePage() {
        // Always load participants on page initialization
        this.loadParticipants();
    }

    handleAgendaFocus() {
        console.log('ParticipantsManager: handleAgendaFocus called', {
            agendaOptionsLength: this.agendaOptions.length,
            hasDropdown: !!this.agendaDropdown,
            hasOptionsContainer: !!this.optionsContainer
        });

        if (this.agendaOptions.length === 0) {
            console.log('ParticipantsManager: Loading agendas for the first time');
            this.loadAgendas();
        } else {
            console.log('ParticipantsManager: Showing existing options');
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
                this.loadAgendas();
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

    handleKeyNavigation(e) {
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

    handleGlobalClick(e) {
        if (!e.target.closest('#agenda_search') && !e.target.closest('#agenda_dropdown')) {
            this.hideDropdown();
        }
    }

    handleReset(e) {
        e.preventDefault();
        this.agendaSearch.value = '';
        this.agendaIdInput.value = '';
        this.currentAgendaId = '';
        this.loadParticipants();
        this.cachedResults.clear();
        this.agendaOptions = [];
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

    async loadAgendas(searchTerm = '') {
        this.showDropdownLoading(true);

        // Cancel previous request if exists
        if (this.abortController) {
            this.abortController.abort();
        }

        // Create new AbortController for this request
        this.abortController = new AbortController();
        this.currentRequest = this.abortController.signal;

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
                },
                signal: this.abortController.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.agendaOptions = data.agendas || [];

                if (searchTerm) {
                    this.cachedResults.set(searchTerm, this.agendaOptions);
                }

                this.displayResults(this.agendaOptions, searchTerm);
            } else {
                this.showDropdownError(data.message || 'Gagal memuat data agenda');
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Request was cancelled');
                return;
            }

            console.error('Error loading agendas:', error);
            this.showDropdownError('Terjadi kesalahan saat memuat data agenda');
        } finally {
            this.currentRequest = null;
            this.abortController = null;
            this.showDropdownLoading(false);
        }
    }

    searchAgendas(searchTerm) {
        if (this.agendaOptions.length > 0) {
            const filtered = this.agendaOptions.filter(agenda =>
                agenda.nama_agenda.toLowerCase().includes(searchTerm)
            );

            if (filtered.length > 0) {
                this.displayResults(filtered, searchTerm);
                return;
            }
        }

        this.loadAgendas(searchTerm);
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

        if (searchTerm && searchTerm.length > 0) {
            const regex = new RegExp('(' + this.escapeRegex(searchTerm) + ')', 'gi');
            displayText = displayText.replace(regex, '<mark class="bg-warning">$1</mark>');
        }

        option.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="font-weight-medium">${displayText}</div>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ${agenda.tanggal_mulai ? new Date(agenda.tanggal_mulai).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia'}
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
                this.currentAgendaName = agendaName;

                // Hide dropdown
                this.hideDropdown();

                // Remove active class from all options
                options.forEach(opt => opt.classList.remove('active'));

                // Add active class to selected option
                option.classList.add('active');

                // Load participants for selected agenda
                this.loadParticipants(agendaId, agendaName);
            });
        });
    }

    showAllOptions() {
        if (this.agendaOptions.length > 0) {
            this.displayResults(this.agendaOptions);
        } else {
            this.loadAgendas();
        }
    }

    showDropdownLoading(show) {
        if (this.loadingMessage) this.loadingMessage.style.display = show ? 'block' : 'none';
        if (this.loadingIndicator) this.loadingIndicator.style.display = show ? 'block' : 'none';
        if (this.noResults) this.noResults.style.display = 'none';

        if (show && this.agendaDropdown) {
            this.agendaDropdown.style.display = 'block';
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
        }
        if (this.noResults) this.noResults.style.display = 'none';
        if (this.loadingMessage) this.loadingMessage.style.display = 'none';
    }

    setActiveOption(options, index) {
        options.forEach(opt => opt.classList.remove('active'));

        if (options[index]) {
            options[index].classList.add('active');
            options[index].scrollIntoView({ block: 'nearest' });
        }
    }

    async loadParticipants(agendaId = '', agendaName = '') {
        this.showParticipantsLoading(true);

        try {
            const url = new URL(window.routes?.admin?.participants?.load || '/admin/participants/load', window.location.origin);
            if (agendaId || this.currentAgendaId) {
                url.searchParams.append('agenda_id', agendaId || this.currentAgendaId);
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
                this.displayParticipants(data);
            } else {
                this.showParticipantsError('Gagal memuat data peserta');
            }
        } catch (error) {
            console.error('Error loading participants:', error);
            this.showParticipantsError('Terjadi kesalahan saat memuat data peserta');
        } finally {
            this.showParticipantsLoading(false);
        }
    }

    displayParticipants(data) {
        // Hide all containers
        this.hideAllParticipantContainers();

        if (data.total === 0) {
            this.showEmptyState();
        } else {
            this.showParticipantsTable(data);
            this.showPagination(data);
            this.showParticipantsInfo(data);
        }

        this.updateExportButton(data);
    }

    hideAllParticipantContainers() {
        if (this.participantsTableContainer) this.participantsTableContainer.style.display = 'none';
        if (this.participantsPagination) this.participantsPagination.style.display = 'none';
        if (this.participantsEmpty) this.participantsEmpty.style.display = 'none';
        if (this.participantsInfo) this.participantsInfo.style.display = 'none';
    }

    showEmptyState() {
        if (this.participantsEmpty) {
            this.participantsEmpty.style.display = 'block';
        }
    }

    showParticipantsTable(data) {
        if (this.participantsTableContainer) {
            this.participantsTableContainer.innerHTML = data.html;
            this.participantsTableContainer.style.display = 'block';
        }
    }

    showPagination(data) {
        if (this.participantsPagination && data.pagination?.has_pages) {
            this.participantsPagination.innerHTML = this.generatePagination(data.pagination);
            this.participantsPagination.style.display = 'block';
            // Bind events to pagination links
            this.bindPaginationEvents();
        }
    }

    showParticipantsInfo(data) {
        if (this.participantsInfo && data.pagination) {
            if (this.currentPageSpan) this.currentPageSpan.textContent = data.pagination.current_page;
            if (this.lastPageSpan) this.lastPageSpan.textContent = data.pagination.last_page;
            if (this.totalParticipantsSpan) this.totalParticipantsSpan.textContent = data.pagination.total;
            this.participantsInfo.style.display = 'block';
        }
    }

    updateExportButton(data) {
        if (!this.exportPdfBtn) return;

        if (data.agenda) {
            const exportUrl = new URL(window.routes?.participants?.exportPdf || '/participants/export-pdf', window.location.origin);
            exportUrl.searchParams.append('agenda_id', data.agenda.id);
            this.exportPdfBtn.href = exportUrl.toString();
        } else {
            this.exportPdfBtn.href = window.routes?.participants?.exportPdf || '/participants/export-pdf';
        }
    }

    generatePagination(pagination) {
        let paginationHtml = '<nav aria-label="Participants pagination"><ul class="pagination">';

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
        if (!this.participantsPagination) return;
        const links = this.participantsPagination.querySelectorAll('a.page-link[data-page]');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.getAttribute('data-page'), 10);
                if (!Number.isNaN(page)) {
                    this.loadParticipantsPage(page);
                }
            });
        });
    }

    async loadParticipantsPage(page) {
        this.showParticipantsLoading(true);

        try {
            const url = new URL(window.routes?.admin?.participants?.load || '/admin/participants/load', window.location.origin);

            // Add current filters
            if (this.currentAgendaId) {
                url.searchParams.append('agenda_id', this.currentAgendaId);
            }

            // Add search parameter if there's text in the search box
            if (this.agendaSearch?.value.trim()) {
                url.searchParams.append('search', this.agendaSearch.value.trim());
            }

            // Add page parameter
            url.searchParams.append('page', page);

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
                this.displayParticipants(data);
            } else {
                this.showParticipantsError('Gagal memuat data peserta');
            }
        } catch (error) {
            console.error('Error loading participants page:', error);
            this.showParticipantsError('Terjadi kesalahan saat memuat halaman peserta');
        } finally {
            this.showParticipantsLoading(false);
        }
    }

    showParticipantsLoading(show) {
        if (show) {
            if (this.participantsLoading) this.participantsLoading.style.display = 'block';
            this.hideAllParticipantContainers();
        } else {
            if (this.participantsLoading) this.participantsLoading.style.display = 'none';
        }
    }

    showParticipantsError(message) {
        if (this.participantsTableContainer) {
            this.participantsTableContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
            this.participantsTableContainer.style.display = 'block';
        }
    }

    bindDeleteEvents() {
        // Use event delegation for dynamically loaded content
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-participant-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-participant-btn');
                this.handleDelete(button);
            }
        });
    }

    async handleDelete(button) {
        const route = button.getAttribute('data-route');
        const confirmMessage = button.getAttribute('data-confirm-message');
        const successMessage = button.getAttribute('data-success-message');
        const errorMessage = button.getAttribute('data-error-message');
        const reloadPage = button.getAttribute('data-reload-page') === 'true';

        // Extract participant ID from route for tracking
        const participantId = route.split('/').pop();

        // Prevent multiple requests for the same participant
        if (this.processingDeletes.has(participantId)) {
            return;
        }

        // Show confirmation dialog only once
        if (button.dataset.confirmed !== 'true') {
            if (!confirm(confirmMessage)) {
                return;
            }
            button.dataset.confirmed = 'true';
        }

        // Mark as processing
        this.processingDeletes.add(participantId);

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

                if (reloadPage) {
                    // Reload page for server-side mode
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Reload participants for dynamic mode
                    this.loadParticipants();
                }
            } else if (response.status === 404 && data.message === 'Peserta tidak ditemukan atau sudah dihapus.') {
                // Participant already deleted (possibly due to double request), treat as success
                console.log('Participant already deleted, treating as success');
                this.showToast(successMessage, 'success');
                if (!reloadPage) {
                    this.loadParticipants();
                }
            } else {
                // Show specific error message from server
                const errorMsg = data.message || errorMessage;
                this.showToast(errorMsg, 'error');
                console.error('Delete failed:', data);
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showToast(error.message || errorMessage, 'error');
        } finally {
            // Clean up
            this.processingDeletes.delete(participantId);
            this.setButtonLoading(button, false);
            delete button.dataset.confirmed;
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

// Global function to load participants page (called from generated pagination)
window.loadParticipantsPage = function(page) {
    if (window.participantsManager) {
        window.participantsManager.loadParticipantsPage(page);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.participantsManager = new ParticipantsManager();
});
