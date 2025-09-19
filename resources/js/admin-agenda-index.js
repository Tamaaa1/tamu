$(document).ready(function() {
    // Handler untuk form toggle link active
    $('.toggle-link-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalButtonContent = button.html();
        const agendaId = form.data('agenda-id');
        const row = form.closest('tr');

        // Disable button dan tampilkan loading
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');

        // Kirim AJAX request
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update button appearance berdasarkan status baru
                    if (response.link_active) {
                        button.removeClass('btn-secondary').addClass('btn-success');
                        button.attr('title', 'Nonaktifkan Link & QR Code');
                        button.html('<i class="fas fa-toggle-on"></i>');

                        // Update kolom Link Acara
                        const linkColumn = row.find('.link-column');
                        linkColumn.html(`
                            <a href="${response.qrcode_url}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-qrcode"></i> QR Code
                            </a>
                        `);
                    } else {
                        button.removeClass('btn-success').addClass('btn-secondary');
                        button.attr('title', 'Aktifkan Link & QR Code');
                        button.html('<i class="fas fa-toggle-off"></i>');

                        // Update kolom Link Acara
                        const linkColumn = row.find('.link-column');
                        linkColumn.html('<span class="text-muted">Tidak Aktif</span>');
                    }

                    // Tampilkan notifikasi sukses
                    showNotification('success', response.message);
                } else {
                    // Tampilkan pesan error
                    showNotification('error', response.message || 'Terjadi kesalahan');
                    button.html(originalButtonContent);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                let errorMessage = 'Terjadi kesalahan saat mengubah status link';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                showNotification('error', errorMessage);
                button.html(originalButtonContent);
            },
            complete: function() {
                // Re-enable button
                button.prop('disabled', false);
            }
        });
    });

    // Handle pagination clicks with AJAX for agenda index
    $(document).on('click', '#pagination-container a.page-link', function(e) {
        const $link = $(this);
        const isNextPrev = $link.parent().hasClass('page-item') && ($link.text().trim() === 'Next' || $link.text().trim() === 'Previous');
        if (isNextPrev) {
            // Allow default navigation for Next/Previous links
            return;
        }
        e.preventDefault();
        const url = new URL($link.attr('href'));
        const page = url.searchParams.get('page');

        if (page) {
            // Show loading spinner
            $('#loading-spinner').show();
            $('#table-container').css('opacity', '0.5').css('pointer-events', 'none');

            // Fetch data with page parameter
            $.ajax({
                url: '{{ route("agenda.data") }}',
                data: { page: page },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    if (data.success) {
                        // Update table body
                        let html = '';
                        data.data.forEach(function(agenda) {
                            html += `
                                <tr>
                                    <td>${agenda.no}</td>
                                    <td><strong>${agenda.nama_agenda}</strong></td>
                                    <td><span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 150px;">${agenda.dinas}</span></td>
                                    <td>${agenda.tanggal_agenda}</td>
                                    <td><span class="badge badge-secondary">${agenda.koordinator}</span></td>
                                    <td class="link-column">`;
                            if (agenda.link_active) {
                                html += `<a href="${agenda.qrcode_url}" class="btn btn-sm btn-outline-info"><i class="fas fa-qrcode"></i> QR Code</a>`;
                            } else {
                                html += `<span class="text-muted">Tidak Aktif</span>`;
                            }
                            html += `</td>
                                    <td><span class="badge badge-success">${agenda.peserta_count} Peserta</span></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/admin/agenda/${agenda.id}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="/admin/agenda/${agenda.id}/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <form action="/admin/agenda/toggle-link/${agenda.id}" method="POST" class="d-inline toggle-link-form" data-agenda-id="${agenda.id}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm ${agenda.link_active ? 'btn-success' : 'btn-secondary'}" title="${agenda.link_active ? 'Nonaktifkan Link & QR Code' : 'Aktifkan Link & QR Code'}">
                                                    <i class="fas ${agenda.link_active ? 'fa-toggle-on' : 'fa-toggle-off'}"></i>
                                                </button>
                                            </form>
                                            <form action="/admin/agenda/${agenda.id}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#agendas-tbody').html(html);

                        // Update pagination links
                        $('#pagination-container').html(data.pagination.links);

                        // Update pagination info
                        $('#pagination-info').html(`Halaman ${data.pagination.current_page} dari ${data.pagination.last_page} (Total: ${data.pagination.total} data)`);

                        // Hide loading spinner
                        $('#loading-spinner').hide();
                        $('#table-container').css('opacity', '1').css('pointer-events', 'auto');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading agenda data:', xhr);
                    $('#loading-spinner').hide();
                    $('#table-container').css('opacity', '1').css('pointer-events', 'auto');
                }
            });
        }
    });
});

// Fungsi untuk menampilkan notifikasi
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass} me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Hapus alert lama jika ada
    $('.alert').remove();

    // Tambahkan alert baru di atas card
    $('.card.shadow').before(alertHtml);

    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
