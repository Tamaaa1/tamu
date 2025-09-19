$(document).ready(function() {
    // Initialize Select2 for Dinas dropdown
    $('#dinas_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Cari dan pilih dinas...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Dinas tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            },
            inputTooShort: function() {
                return "Ketik minimal 1 karakter untuk mencari";
            },
            loadingMore: function() {
                return "Memuat lebih banyak...";
            }
        },
        // Enable search on mobile devices
        minimumInputLength: 0,
        // Custom matching function for better search
        matcher: function(params, data) {
            // If there are no search terms, return all data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Skip if there is no 'text' property
            if (typeof data.text === 'undefined') {
                return null;
            }

            // Check if the text contains the term (case insensitive)
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            // Return `null` if the term should not be displayed
            return null;
        }
    });

    // Handle Select2 validation
    $('#dinas_id').on('change', function() {
        const value = $(this).val();
        const errorElement = $('#dinas_id-error');
        
        if (value) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            errorElement.text('').hide();
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
            errorElement.text('Dinas harus dipilih').show();
        }
    });

    // Handle form validation
    $('#registrationForm').on('submit', function(e) {
        let isValid = true;
        
        // Validate dinas selection
        const dinasValue = $('#dinas_id').val();
        const dinasError = $('#dinas_id-error');
        
        if (!dinasValue) {
            $('#dinas_id').removeClass('is-valid').addClass('is-invalid');
            dinasError.text('Dinas harus dipilih').show();
            isValid = false;
        } else {
            $('#dinas_id').removeClass('is-invalid').addClass('is-valid');
            dinasError.text('').hide();
        }

        if (!isValid) {
            e.preventDefault();
            
            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
    });

    // Handle responsive behavior
    function handleResponsive() {
        if ($(window).width() < 768) {
            // On mobile, make dropdown take full width
            $('.select2-container').css('width', '100%');
            
            // Adjust dropdown positioning on mobile
            $('#dinas_id').select2({
                dropdownParent: $('#dinas_id').parent(),
                theme: 'bootstrap-5',
                placeholder: 'Cari dan pilih dinas...',
                allowClear: true,
                width: '100%'
            });
        }
    }

    // Initialize responsive handling
    handleResponsive();
    $(window).resize(handleResponsive);

    // Preselect if there's old value (for form validation errors)
    @if(old('dinas_id'))
        $('#dinas_id').val('{{ old("dinas_id") }}').trigger('change');
    @endif
});

// Enhanced form submission with loading state
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftar...';
    submitBtn.disabled = true;
    
    // If validation fails, restore button
    setTimeout(function() {
        if (document.querySelector('.is-invalid')) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 100);
});
