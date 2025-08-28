document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const submitButton = document.querySelector('button[type="submit"]');
    const form = document.querySelector('form');
    
    if (!passwordInput) return;
    
    // Periksa apakah ini formulir edit (password bersifat opsional)
    const isEditForm = passwordInput.placeholder.includes('Kosongkan');
    
    // Membuat indikator validasi
    const validationContainer = document.createElement('div');
    validationContainer.className = 'password-validation mt-2';
    validationContainer.innerHTML = `
        <div class="validation-item" id="length-validation">
            <span class="validation-icon">❌</span>
            <span class="validation-text">Minimal 8 karakter</span>
        </div>
        <div class="validation-item" id="uppercase-validation">
            <span class="validation-icon">❌</span>
            <span class="validation-text">Mengandung huruf kapital</span>
        </div>
        <div class="validation-item" id="special-validation">
            <span class="validation-icon">❌</span>
            <span class="validation-text">Mengandung karakter khusus (_, @, #, $, dll)</span>
        </div>
        <div class="validation-item" id="match-validation">
            <span class="validation-icon">❌</span>
            <span class="validation-text">Password cocok</span>
        </div>
    `;
    
    passwordInput.parentNode.appendChild(validationContainer);
    
    // Add CSS styles
    const style = document.createElement('style');
    style.textContent = `
        .password-validation {
            font-size: 14px;
        }
        .validation-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .validation-icon {
            margin-right: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .validation-text {
            flex: 1;
        }
        .valid {
            color: #28a745;
        }
        .invalid {
            color: #dc3545;
        }
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .hidden {
            display: none;
        }
    `;
    document.head.appendChild(style);
    
    // Membuat indikator validasi
    function validatePassword(password, confirmPassword) {
        // For edit form: if password is empty, skip validation
        if (isEditForm && password === '') {
            hideValidation();
            submitButton.disabled = false;
            submitButton.classList.remove('btn-disabled');
            return true;
        }
        
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        const passwordsMatch = password === confirmPassword && password !== '';
        
        // Update Indikator Validasi
        updateValidation('length-validation', hasLength);
        updateValidation('uppercase-validation', hasUppercase);
        updateValidation('special-validation', hasSpecial);
        updateValidation('match-validation', passwordsMatch);
        
        // Show validation container
        validationContainer.classList.remove('hidden');
        
        // Enable/disable submit button
        const isValid = hasLength && hasUppercase && hasSpecial && passwordsMatch;
        submitButton.disabled = !isValid;
        submitButton.classList.toggle('btn-disabled', !isValid);
        
        return isValid;
    }
    
    function updateValidation(elementId, isValid) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.remove('valid', 'invalid');
            element.classList.add(isValid ? 'valid' : 'invalid');
            
            const icon = element.querySelector('.validation-icon');
            if (icon) {
                icon.textContent = isValid ? '✅' : '❌';
            }
        }
    }
    
    function hideValidation() {
        validationContainer.classList.add('hidden');
    }
    
    // Event listeners
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value, confirmPasswordInput ? confirmPasswordInput.value : '');
    });
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            validatePassword(passwordInput.value, this.value);
        });
    }
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (isEditForm && passwordInput.value === '') {
            return true; // Allow submission if password is empty in edit form
        }
        
        if (!validatePassword(passwordInput.value, confirmPasswordInput ? confirmPasswordInput.value : '')) {
            e.preventDefault();
            alert('Silakan perbaiki validasi password sebelum melanjutkan.');
        }
    });
    
    // Initial validation
    if (isEditForm && passwordInput.value === '') {
        hideValidation();
    } else {
        validatePassword(passwordInput.value, confirmPasswordInput ? confirmPasswordInput.value : '');
    }
});
