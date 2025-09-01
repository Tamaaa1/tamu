// Public Registration Form JavaScript
class PublicRegistrationForm {
    constructor() {
        this.form = document.getElementById('registrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.signatureInput = document.getElementById('signatureInput');
        this.agendaSelect = document.getElementById('agenda_id');
        this.selectedAgendaInput = document.getElementById('selected_agenda_id');
        this.agendaTitle = document.getElementById('agendaTitle');
        
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Agenda selection change
        this.agendaSelect.addEventListener('change', () => this.handleAgendaChange());

        // Input validation
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => this.clearFieldError(input.name));
        });
    }

    handleSubmit(e) {
        e.preventDefault();
        
        this.clearErrors();
        
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
        const originalText = this.submitBtn.innerHTML;
        
        this.setLoadingState(true);
        this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

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
                this.form.reset();
                window.signaturePadManager.clear();
                document.querySelector('.signature-placeholder').style.display = 'flex';
            } else {
                this.showErrors(data.errors);
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.errors) {
                this.showErrors(error.errors);
            } else {
                this.showError('general', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
            }
        } finally {
            this.setLoadingState(false);
            this.submitBtn.innerHTML = originalText;
        }
    }

    handleAgendaChange() {
        const selectedAgendaId = this.agendaSelect.value;
        this.selectedAgendaInput.value = selectedAgendaId;
        
        // Update agenda title
        const selectedOption = this.agendaSelect.options[this.agendaSelect.selectedIndex];
        this.agendaTitle.innerText = selectedOption.text;
    }

    clearFieldError(fieldName) {
        const errorElement = document.getElementById(fieldName + '-error');
        if (errorElement) {
            errorElement.textContent = '';
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
        const successElement = document.getElementById('successMessage');
        successElement.style.display = 'block';
        
        // Hide QR code container if exists
        const qrContainer = document.getElementById('qrCodeContainer');
        if (qrContainer) {
            qrContainer.style.display = 'none';
        }
        
        successElement.scrollIntoView({ behavior: 'smooth' });
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
