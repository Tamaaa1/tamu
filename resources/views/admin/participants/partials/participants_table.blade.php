<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Instansi</th>
                <th>No HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $index => $participant)
                <tr>
                    <td>{{ $participants->firstItem() + $index }}</td>
                    <td>{{ $participant->nama }}</td>
                    <td>{{ $participant->jabatan }}</td>
                    <td>
                        <span class="badge badge-info d-inline-block" style="word-wrap: break-word; white-space: normal; max-width: 150px;">
                            {{ $participant->masterDinas->nama_dinas ?? 'N/A' }}
                        </span>
                    </td>
                    <td>{{ $participant->no_hp }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger delete-participant-btn"
                                    data-route="{{ route('admin.participants.destroy', $participant) }}"
                                    data-confirm-message="Yakin ingin menghapus peserta ini?"
                                    data-success-message="Peserta berhasil dihapus!"
                                    data-error-message="Gagal menghapus peserta. Silakan coba lagi."
                                    data-reload-page="false">
                                <i class="fas fa-trash"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Belum ada peserta yang terdaftar</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
