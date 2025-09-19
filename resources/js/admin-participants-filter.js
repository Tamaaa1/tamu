document.addEventListener('DOMContentLoaded', function() {
    const agendaSearch = document.getElementById('agenda_search');
    const agendaDropdown = document.getElementById('agenda_dropdown');
    const agendaIdInput = document.getElementById('agenda_id');
    const noResults = document.getElementById('no_results');
    const agendaOptions = document.querySelectorAll('.agenda-option');

    // Show dropdown when input is focused
    agendaSearch.addEventListener('focus', function() {
        showAllOptions();
        agendaDropdown.style.display = 'block';
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#agenda_search') && !e.target.closest('#agenda_dropdown')) {
            agendaDropdown.style.display = 'none';
        }
    });

    // Filter options based on search input
    agendaSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let hasResults = false;

        agendaOptions.forEach(function(option) {
            const agendaName = option.getAttribute('data-name').toLowerCase();

            if (agendaName.includes(searchTerm)) {
                option.style.display = 'block';
                hasResults = true;

                // Highlight matching text
                const originalText = option.getAttribute('data-name');
                const highlightedText = originalText.replace(
                    new RegExp('(' + searchTerm + ')', 'gi'),
                    '<mark class="bg-warning">$1</mark>'
                );
                option.innerHTML = highlightedText;
            } else {
                option.style.display = 'none';
            }
        });

        // Show/hide "no results" message
        if (hasResults) {
            noResults.style.display = 'none';
        } else {
            noResults.style.display = 'block';
        }

        // If search is empty, reset all options
        if (searchTerm === '') {
            agendaIdInput.value = '';
            showAllOptions();
        }

        agendaDropdown.style.display = 'block';
    });

    // Handle option selection
    agendaOptions.forEach(function(option) {
        option.addEventListener('click', function(e) {
            e.preventDefault();

            const agendaId = this.getAttribute('data-id');
            const agendaName = this.getAttribute('data-name');

            agendaSearch.value = agendaName;
            agendaIdInput.value = agendaId;
            agendaDropdown.style.display = 'none';

            // Remove active class from all options
            agendaOptions.forEach(opt => opt.classList.remove('active'));

            // Add active class to selected option
            this.classList.add('active');
        });
    });

    function showAllOptions() {
        agendaOptions.forEach(function(option) {
            option.style.display = 'block';
            option.innerHTML = option.getAttribute('data-name');
        });
        noResults.style.display = 'none';
    }
});
