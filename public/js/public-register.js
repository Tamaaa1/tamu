// Public Registration Form JavaScript
class PublicRegistrationForm {
    constructor() {
        this.form = document.getElementById('registrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.signatureInput = document.getElementById('signatureInput');
        this.agendaSelect = document.getElementById('agenda_id');
        this.selectedAgendaInput = document.getElementById('selected_agenda_id');
        this.agendaTitle = document.getElementById('agendaTitle');

        // Check if required elements exist
        const missing = [];
        if (!this.form) missing.push('registrationForm');
        if (!this.submitBtn) missing.push('submitBtn');
        if (!this.signatureInput) missing.push('signatureInput');

        if (missing.length > 0) {
            console.error('Required form elements not found:', missing);
            return;
        }

        this.init();
    }

    init() {
        this.initializeSelect2();
        this.bindEvents();
    }

    initializeSelect2() {
        // Initialize Select2 for instansi dropdown
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-instansi').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik untuk mencari instansi...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Instansi tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function() {
                        return "Ketik untuk mencari";
                    }
                }
            });

            // Clear error on change
            $('.select2-instansi').on('change', () => {
                this.clearFieldError('dinas_id');
                this.handleDinasChange();
            });

            // Handle Select2 opening to ensure proper z-index
            $('.select2-instansi').on('select2:open', function() {
                document.querySelector('.select2-search__field').focus();
            });
        }
    }

    bindEvents() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Agenda selection change
        if (this.agendaSelect) {
            this.agendaSelect.addEventListener('change', () => this.handleAgendaChange());
        }

        // Manual dinas input change
        const manualDinas = document.getElementById('manual_dinas');
        if (manualDinas) {
            manualDinas.addEventListener('input', () => this.clearFieldError('manual_dinas'));
            manualDinas.addEventListener('blur', () => this.validateField('manual_dinas'));
        }

        // Real-time validation
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => this.clearFieldError(input.name));
            input.addEventListener('blur', () => this.validateField(input.name));
        });

        // Special handling for Select2
        $('.select2-instansi').on('change', () => {
            this.clearFieldError('dinas_id');
            this.validateField('dinas_id');
            this.handleDinasChange();
        });
    }

    handleSubmit(e) {
        e.preventDefault();

        this.clearErrors();

        // Client-side validation
        if (!this.validateAll()) {
            toastr.error('Mohon perbaiki kesalahan pada formulir sebelum mengirimkan.', 'Validasi Gagal');
            return;
        }

        // Validate signature using signaturePadManager
        const signatureError = window.signaturePadManager.validate();
        if (signatureError) {
            this.showError('signature', signatureError);
            return;
        }

        // Update signature input
        if (!this.signatureInput.value) {
            const signatureData = window.signaturePadManager.toDataURL();
            this.signatureInput.value = signatureData;
        }

        this.submitForm();
    }

    async submitForm() {
        const formData = new FormData(this.form);

        this.setLoadingState(true);

        try {
            const response = await fetch('/agenda/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            if (data.success) {
                this.showSuccess(data.message);
                this.resetForm();
            } else {
                this.showErrors(data.errors);
                window.showError('Mohon perbaiki kesalahan yang ditampilkan pada formulir.');
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.errors) {
                this.showErrors(error.errors);
                window.showError('Mohon perbaiki kesalahan yang ditampilkan pada formulir.');
            } else {
                window.showError('Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
            }
        } finally {
            this.setLoadingState(false);
        }
    }

    resetForm() {
        this.form.reset();

        // Reset Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-instansi').val(null).trigger('change');
        }

        // Hide manual dinas input
        const manualDinas = document.getElementById('manual_dinas');
        if (manualDinas) {
            manualDinas.style.display = 'none';
            manualDinas.required = false;
        }

        // Reset signature
        if (window.signaturePadManager) {
            window.signaturePadManager.clear();
        }

        const placeholder = document.querySelector('.signature-placeholder');
        if (placeholder) {
            placeholder.style.display = 'flex';
        }


    }

    handleAgendaChange() {
        if (!this.agendaSelect) return;

        const selectedAgendaId = this.agendaSelect.value;
        if (this.selectedAgendaInput) {
            this.selectedAgendaInput.value = selectedAgendaId;
        }

        // Update agenda title
        if (this.agendaTitle) {
            const selectedOption = this.agendaSelect.options[this.agendaSelect.selectedIndex];
            this.agendaTitle.innerText = selectedOption ? selectedOption.text : '';
        }
    }

    handleDinasChange() {
        const dinasSelect = document.getElementById('dinas_id');
        const manualDinas = document.getElementById('manual_dinas');
        if (!dinasSelect || !manualDinas) return;

        if (dinasSelect.value === 'other') {
            manualDinas.style.display = 'block';
            manualDinas.required = true;
        } else {
            manualDinas.style.display = 'none';
            manualDinas.required = false;
            manualDinas.value = '';
            this.clearFieldError('manual_dinas');
        }
    }

    // Validation methods
    validateField(fieldName) {
        const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (!field) return true;

        const value = field.value.trim();
        let isValid = true;
        let message = '';

        switch (fieldName) {
            case 'nama':
                if (!value) {
                    message = 'Nama lengkap wajib diisi';
                    isValid = false;
                } else if (value.length < 2) {
                    message = 'Nama minimal 2 karakter';
                    isValid = false;
                } else if (value.length > 100) {
                    message = 'Nama maksimal 100 karakter';
                    isValid = false;
                } else if (!/^[a-zA-Z\s\.\-\']+$/.test(value)) {
                    message = 'Nama hanya boleh berisi huruf, spasi, titik, strip, dan apostrof';
                    isValid = false;
                } else {
                    message = 'Nama valid';
                }
                break;

            case 'jabatan':
                if (!value) {
                    message = 'Jabatan wajib diisi';
                    isValid = false;
                } else if (value.length < 2) {
                    message = 'Jabatan minimal 2 karakter';
                    isValid = false;
                } else if (value.length > 100) {
                    message = 'Jabatan maksimal 100 karakter';
                    isValid = false;
                } else {
                    message = 'Jabatan valid';
                }
                break;

            case 'no_hp':
                if (!value) {
                    message = 'Nomor HP wajib diisi';
                    isValid = false;
                } else {
                    const cleanNumber = value.replace(/\s/g, '');
                    if (!/^(\+62|62|0)[8-9][0-9]{7,11}$/.test(cleanNumber)) {
                        message = 'Format nomor HP tidak valid (contoh: 081234567890)';
                        isValid = false;
                    } else if (cleanNumber.length < 10 || cleanNumber.length > 13) {
                        message = '✗Nomor HP harus 10-13 digit';
                        isValid = false;
                    } else {
                        message = '✓ Nomor HP valid';
                    }
                }
                break;

            case 'gender':
                if (!value) {
                    message = 'Jenis kelamin wajib dipilih';
                    isValid = false;
                } else {
                    message = 'Jenis kelamin dipilih';
                }
                break;

            case 'dinas_id':
                if (!value) {
                    message = 'Instansi wajib dipilih';
                    isValid = false;
                } else {
                    message = 'Instansi dipilih';
                }
                break;

            case 'manual_dinas':
                const dinasSelect = document.getElementById('dinas_id');
                if (dinasSelect && dinasSelect.value === 'other') {
                    if (!value) {
                        message = 'Nama instansi wajib diisi';
                        isValid = false;
                    } else if (value.length < 2) {
                        message = 'Nama instansi minimal 2 karakter';
                        isValid = false;
                    } else if (value.length > 255) {
                        message = 'Nama instansi maksimal 255 karakter';
                        isValid = false;
                    } else {
                        message = 'Nama instansi valid';
                    }
                }
                break;
        }

        // Update field visual state
        this.updateFieldState(field, isValid, value);

        if (!isValid || (isValid && value)) {
            this.showError(fieldName, message);
        }



        return isValid;
    }

    validateAll() {
        const fields = ['nama', 'gender', 'jabatan', 'no_hp', 'dinas_id'];
        let allValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                allValid = false;
            }
        });

        // Check manual dinas if needed
        const dinasSelect = document.getElementById('dinas_id');
        if (dinasSelect && dinasSelect.value === 'other') {
            if (!this.validateField('manual_dinas')) {
                allValid = false;
            }
        }

        return allValid;
    }

    updateFieldState(field, isValid, value) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;

        // Remove existing validation classes
        formGroup.classList.remove('is-valid', 'is-invalid');

        // Add appropriate class
        if (value) {
            formGroup.classList.add(isValid ? 'is-valid' : 'is-invalid');
        }
    }

    clearFieldError(fieldName) {
        const errorElement = document.getElementById(fieldName + '-error');
        if (errorElement) {
            errorElement.textContent = '';
        }

        // Clear field state
        const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const formGroup = field.closest('.form-group');
            if (formGroup) {
                formGroup.classList.remove('is-valid', 'is-invalid');
            }
        }
    }

    clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.getElementById('errorMessage').style.display = 'none';
    }

    showError(field, message) {
        const errorElement = document.getElementById(field + '-error');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    showErrors(errors) {
        Object.keys(errors).forEach(field => {
            this.showError(field, errors[field][0]);
        });
    }

    showSuccess(message) {
        const popup = document.getElementById('customSuccessPopup');
        popup.style.display = 'flex';

        // Scroll to the popup for better visibility
        popup.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Hide QR code container if exists
        const qrContainer = document.getElementById('qrCodeContainer');
        if (qrContainer) {
            qrContainer.style.display = 'none';
        }
    }



    setLoadingState(loading) {
        this.submitBtn.disabled = loading;
        if (loading) {
            this.submitBtn.classList.add('btn-loading');
        } else {
            this.submitBtn.classList.remove('btn-loading');
        }
    }
}

// Close custom popup function
function closeCustomPopup() {
    const popup = document.getElementById('customSuccessPopup');
    popup.style.display = 'none';
}

// Copy to clipboard function
function copyToClipboard(text, event) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.publicRegistrationForm = new PublicRegistrationForm();
});