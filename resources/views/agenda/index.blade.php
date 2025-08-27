<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt me-2"></i>Manajemen Agenda</h2>
            <a href="{{ route('agenda.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Buat Agenda Baru
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Agenda</th>
                                <th>Dinas</th>
                                <th>Tanggal</th>
                                <th>Koordinator</th>
                                <th>Link Acara</th>
                                <th>Peserta</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $index => $agenda)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $agenda->nama_agenda }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $agenda->koordinator->name ?? $agenda->nama_koordinator }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ $agenda->link_acara }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Link
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{ $agenda->link_acara }}')">
                                            <i class="fas fa-copy me-1"></i>Copy
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $agenda->agendaDetail->count() }} Peserta</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('agenda.show', $agenda) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('agenda.public', $agenda->link_acara) }}" target="_blank" class="btn btn-sm btn-success">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                            <form action="{{ route('agenda.destroy', $agenda) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>Belum ada agenda yang dibuat</p>
                                        <a href="{{ route('agenda.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Buat Agenda Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
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
    </script>
</body>
</html>
