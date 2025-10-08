@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Manajemen Instansi</h3>
                        @if($dinas->hasPages())
                            <small class="text-muted">
                                Halaman {{ $dinas->currentPage() }} dari {{ $dinas->lastPage() }}
                                (Total: {{ $dinas->total() }} data)
                            </small>
                        @endif
                    </div>
                    <a href="{{ route('admin.master-dinas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Instansi
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Instansi</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dinas as $index => $dinasItem)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dinasItem->nama_dinas }}</td>
                                        <td>{{ $dinasItem->alamat ?? '-' }}</td>
                                        <td>{{ $dinasItem->email ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.master-dinas.edit', $dinasItem) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.master-dinas.destroy', $dinasItem) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data dinas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $dinas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
