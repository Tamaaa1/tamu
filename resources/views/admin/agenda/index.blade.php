@extends('admin.layouts.app')

@section('title', 'Manajemen Agenda')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Agenda</h1>
    <a href="{{ route('admin.agenda.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Agenda Baru
    </a>
</div>

<!-- Filter Tanggal, Bulan, Tahun -->
<form method="GET" action="{{ route('admin.agenda.index') }}" class="mb-4">
    <div class="input-group">
        <input type="number" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal" min="1" max="31" value="{{ request('tanggal') }}">
        <input type="number" name="bulan" id="bulan" class="form-control" placeholder="Bulan" min="1" max="12" value="{{ request('bulan') }}">
        <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Tahun" min="2000" max="{{ date('Y') }}" value="{{ request('tahun') }}">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.agenda.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Agenda</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
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
                                <span class="badge badge-info">
                                    {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $agenda->koordinator->name ?? $agenda->nama_koordinator }}
                                </span>
                            </td>
                            <td>
                                @if($agenda->link_active)
                                    <a href="{{ route('agenda.public.register', $agenda) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-external-link-alt"></i> Link
                                    </a>
                                    <a href="{{ route('admin.agenda.qrcode', $agenda) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-qrcode"></i> QR Code
                                    </a>
                                @else
                                    <span class="text-muted">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $agenda->agendaDetail->count() }} Peserta</span>
                            </td>
                            <td>
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
                                    <form action="{{ route('admin.agenda.destroy', $agenda) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
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
                                <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary">
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
@endsection
