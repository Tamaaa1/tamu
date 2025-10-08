<div class="btn-group" role="group">
    <a href="{{ route('admin.agenda.show', $agenda) }}" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.agenda.edit', $agenda) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.agenda.toggle-link', $agenda) }}" method="POST" class="d-inline">
        @csrf
        @if($agenda->link_active)
            <button type="submit" class="btn btn-sm btn-success" title="Nonaktifkan Link & QR Code">
                <i class="fas fa-toggle-on"></i>
            </button>
        @else
            <button type="submit" class="btn btn-sm btn-secondary" title="Aktifkan Link & QR Code">
                <i class="fas fa-toggle-off"></i>
            </button>
        @endif
    </form>
    <button type="button" class="btn btn-sm btn-danger delete-agenda-btn"
            data-route="{{ route('admin.agenda.destroy', $agenda) }}"
            data-confirm-message="Yakin ingin menghapus agenda ini?"
            data-success-message="Agenda berhasil dihapus!"
            data-error-message="Gagal menghapus agenda. Silakan coba lagi.">
        <i class="fas fa-trash"></i>
        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
</div>
