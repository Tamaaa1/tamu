@extends('admin.layouts.app')

@section('title', 'Detail Agenda')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Agenda: {{ $agenda->nama_agenda }}</h1>
    <a href="{{ route('admin.agenda.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Agenda Details -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Agenda</h6>
    </div>
    <div class="card-body">
        <p><strong>Instansi:</strong> {{ $agenda->masterDinas->nama_dinas }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</p>
        <p><strong>Koordinator:</strong> {{ $agenda->koordinator->name ?? $agenda->nama_koordinator }}</p>
        <p><strong>Link Acara:</strong> 
            @if(!empty($agenda->unique_token))
                <a href="{{ route('agenda.public.register.token', ['token' => $agenda->unique_token]) }}" target="_blank">Halaman Pendaftaran Publik</a>
            @else
                <span class="text-muted">Token belum tersedia</span>
            @endif
        </p>
    </div>
</div>

<!-- Peserta -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Peserta</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Instansi</th>
                        <th>No HP</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agenda->agendaDetail as $index => $participant)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $participant->nama }}</td>
                            <td>{{ $participant->jabatan }}</td>
                            <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                            <td>{{ $participant->no_hp }}</td>
                            <td>{{ $participant->created_at->format('d/m/Y') }}</td>
                            <td>
                                <form action="{{ route('admin.participants.destroy', $participant) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <p>Belum ada peserta yang terdaftar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
