@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Agendas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Agenda</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAgendas }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Participants Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Peserta</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalParticipants }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Dinas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Dinas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDinas }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Recent Agendas -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                <h6 class="m-0 font-weight-bold text-white">Agenda Terbaru</h6>
                <a href="{{ route('admin.agenda.index') }}" class="btn btn-sm btn-light">
                    Lihat Semua
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                @if($recentAgendas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Agenda</th>
                                    <th>Dinas</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAgendas as $agenda)
                                    <tr>
                                        <td>{{ $agenda->nama_agenda }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.agenda.show', $agenda) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>Belum ada agenda yang dibuat</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Participants -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-success text-white">
                <h6 class="m-0 font-weight-bold text-white">Peserta Terbaru</h6>
                <a href="{{ route('admin.participants.index') }}" class="btn btn-sm btn-light">
                    Lihat Semua
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                @if($recentParticipants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Agenda</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentParticipants as $participant)
                                    <tr>
                                        <td>{{ $participant->nama }}</td>
                                        <td>{{ $participant->jabatan }}</td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ $participant->agenda->nama_agenda ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Belum ada peserta yang mendaftar</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
