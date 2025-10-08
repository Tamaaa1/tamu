/**
 * Admin Forms Validation
 * Client-side validation for all admin forms
 */

class AdminFormValidator {
    constructor() {
        this.forms = {
            agenda: '#agendaForm',
            participant: 'form[action*="participants"]',
            masterDinas: 'form[action*="master-dinas"]'
        };

        this.validationRules = {
            // Agenda form validation rules
            agenda: {
                nama_agenda: {
                    required: true,
                    minLength: 3,
                    maxLength: 255,
                    message: {
                        required: 'Nama agenda wajib diisi',
                        minLength: 'Nama agenda minimal 3 karakter',
                        maxLength: 'Nama agenda maksimal 255 karakter'
                    }
                },
                dinas_id: {
                    required: true,
                    message: {
                        required: 'Instansi wajib dipilih'
                    }
                },
                tanggal_agenda: {
                    required: true,
                    custom: (value) => this.validateFutureDate(value),
                    message: {
                        required: 'Tanggal agenda wajib diisi',
                        custom: 'Tanggal agenda tidak boleh di masa lalu'
                    }
                },
                tempat: {
                    required: true,
                    minLength: 3,
                    maxLength: 255,
                    message: {
                        required: 'Tempat acara wajib diisi',
                        minLength: 'Tempat acara minimal 3 karakter',
                        maxLength: 'Tempat acara maksimal 255 karakter'
                    }
                },
                waktu: {
                    required: true,
                    minLength: 3,
                    message: {
                        required: 'Waktu acara wajib diisi',
                        minLength: 'Waktu acara minimal 3 karakter'
                    }
                },
                link_acara: {
                    custom: (value) => !value || this.validateUrl(value),
                    message: {
                        custom: 'Format URL tidak valid'
                    }
                }
            },

            // Participant form validation rules
            participant: {
                agenda_id: {
                    required: true,
                    message: {
                        required: 'Agenda wajib dipilih'
                    }
                },
                dinas_id: {
                    required: true,
                    message: {
                        required: 'Instansi wajib dipilih'
                    }
                },
                nama: {
                    required: true,
                    custom: (value) => this.validateName(value),
                    minLength: 2,
                    maxLength: 255,
                    message: {
                        required: 'Nama lengkap wajib diisi',
                        custom: 'Nama hanya boleh berisi huruf, spasi, titik, dan apostrof',
                        minLength: 'Nama minimal 2 karakter',
                        maxLength: 'Nama maksimal 255 karakter'
                    }
                },
                gender: {
                    required: true,
                    message: {
                        required: 'Jenis kelamin wajib dipilih'
                    }
                },
                jabatan: {
                    required: true,
                    minLength: 2,
                    maxLength: 255,
                    message: {
                        required: 'Jabatan wajib diisi',
                        minLength: 'Jabatan minimal 2 karakter',
                        maxLength: 'Jabatan maksimal 255 karakter'
                    }
                },
                no_hp: {
                    required: true,
                    custom: (value) => this.validatePhoneNumber(value),
                    message: {
                        required: 'Nomor HP wajib diisi',
                        custom: 'Nomor HP harus 10-13 digit angka'
                    }
                },
                gambar_ttd: {
                    required: true,
                    message: {
                        required: 'Tanda tangan wajib dibuat'
                    }
                }
            },

            // Master Dinas form validation rules
            masterDinas: {
                nama_dinas: {
                    required: true,
                    minLength: 2,
                    maxLength: 255,
                    message: {
                        required: 'Nama instansi wajib diisi',
                        minLength: 'Nama instansi minimal 2 karakter',
                        maxLength: 'Nama instansi maksimal 255 karakter'
                    }
                },
                email: {
                    custom: (value) => !value || this.validateEmail(value),
                    message: {
                        custom: 'Format email tidak valid'
                    }
                }
            }
        };

        this.initialize();
    }

    initialize() {
        // Initialize validation for all forms
        Object.keys(this.forms).forEach(formType => {
            const formSelector = this.forms[formType];
            const form = document.querySelector(formSelector);

            if (form) {
                this.initializeFormValidation(form, formType);
            }
        });
    }

    initializeFormValidation(form, formType) {
        const rules = this.validationRules[formType];
        if (!rules) return;

        // Add validation to form fields
        Object.keys(rules).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.attachFieldValidation(field, rules[fieldName], formType);
            }
        });

        // Add form submit validation
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form, formType)) {
                e.preventDefault();
                this.showFormError(form, 'Mohon perbaiki kesalahan pada form sebelum menyimpan.');
            }
        });
    }

    attachFieldValidation(field, rules, formType) {
        const events = ['blur', 'input', 'change'];

        events.forEach(event => {
            field.addEventListener(event, () => {
                this.validateField(field, rules, formType);
            });
        });
    }

    validateField(field, rules, formType) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        // Check required
        if (rules.required && !value) {
            isValid = false;
            errorMessage = rules.message.required;
        }
        // Check min length
        else if (rules.minLength && value && value.length < rules.minLength) {
            isValid = false;
            errorMessage = rules.message.minLength;
        }
        // Check max length
        else if (rules.maxLength && value && value.length > rules.maxLength) {
            isValid = false;
            errorMessage = rules.message.maxLength;
        }
        // Check custom validation
        else if (rules.custom && !rules.custom(value)) {
            isValid = false;
            errorMessage = rules.message.custom;
        }

        this.updateFieldValidationUI(field, isValid, errorMessage);
        return isValid;
    }

    validateForm(form, formType) {
        const rules = this.validationRules[formType];
        let isFormValid = true;

        Object.keys(rules).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const isFieldValid = this.validateField(field, rules[fieldName], formType);
                if (!isFieldValid) {
                    isFormValid = false;
                }
            }
        });

        return isFormValid;
    }

    updateFieldValidationUI(field, isValid, errorMessage) {
        const formGroup = field.closest('.form-group') || field.closest('.mb-3') || field.closest('.col-md-6');
        const errorElement = formGroup?.querySelector('.error-message') || this.createErrorElement(formGroup);

        // Update field classes
        field.classList.toggle('is-valid', isValid && field.value.trim());
        field.classList.toggle('is-invalid', !isValid && field.value.trim());

        // Update label color
        const label = formGroup?.querySelector('label');
        if (label) {
            label.classList.toggle('text-success', isValid && field.value.trim());
            label.classList.toggle('text-danger', !isValid && field.value.trim());
        }

        // Update error message
        if (errorMessage) {
            errorElement.textContent = errorMessage;
            errorElement.style.display = 'block';
        } else {
            errorElement.style.display = 'none';
        }
    }

    createErrorElement(formGroup) {
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message invalid-feedback d-block';
        errorElement.style.display = 'none';
        formGroup.appendChild(errorElement);
        return errorElement;
    }

    showFormError(form, message) {
        // Use new notification system
        window.showError(message);
    }

    // Validation helper methods
    validateFutureDate(dateString) {
        if (!dateString) return false;
        const selectedDate = new Date(dateString);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return selectedDate >= today;
    }

    validateName(name) {
        // Allow letters, spaces, dots, hyphens, apostrophes
        const nameRegex = /^[a-zA-Z\s.\-']+$/;
        return nameRegex.test(name);
    }

    validatePhoneNumber(phone) {
        // Indonesian phone number: 10-13 digits
        const phoneRegex = /^\d{10,13}$/;
        return phoneRegex.test(phone.replace(/\s+/g, ''));
    }

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    validateUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new AdminFormValidator();
});
