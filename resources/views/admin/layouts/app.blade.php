<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - TAMU</title>

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/css/admin-participants.css', 'resources/js/app.js', 'resources/js/admin-participants-filter.js', 'resources/js/admin-agenda-index.js', 'resources/js/admin-participants-index.js'])

    <!-- AOS Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

<<<<<<< HEAD
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
=======
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

@stack('styles')
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('storage/Pemkot.png') }}" alt="Pemkot Logo" style="width: 40px; height: auto;">
            </div>
            <div class="sidebar-brand-text mx-3">SiMATAMU</div>
        </a>
        <hr class="sidebar-divider my-0">

        <!-- Dashboard -->
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt text-info"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Manajemen</div>

        <li class="nav-item {{ request()->routeIs('admin.agenda.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.agenda.index') }}">
                <i class="fas fa-fw fa-calendar text-warning"></i>
                <span>Agenda</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.participants.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.participants.index') }}">
                <i class="fas fa-fw fa-users text-success"></i>
                <span>Peserta</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.master-dinas.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.master-dinas.index') }}">
                <i class="fas fa-fw fa-building text-primary"></i>
                <span>Instansi</span>
            </a>
        </li>

        @if(Auth::user()->isAdmin())
        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-user-cog text-info"></i>
                <span>Manajemen User</span>
            </a>
        </li>
        @endif

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Sistem</div>

        <!-- Logout -->
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-left" style="width:100%">
                    <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                            <i class="fas fa-user-circle fa-fw"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2"></i>Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End Topbar -->

            <!-- Page Content -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer text-white" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
            <div class="container text-center">
                <p>&copy; Copyright Dinas Komunikasi dan Informatika Kota Pontianak.</p>
            </div>
        </footer>
    </div>
</div>

<!-- Scroll Top -->
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<<<<<<< HEAD
<!-- Scripts - URUTAN PENTING! -->
<!-- 1. jQuery HARUS PERTAMA -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 2. jQuery UI setelah jQuery -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- 3. Select2 setelah jQuery -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- 4. AOS Animation -->
=======
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>

<!-- 5. Custom Scripts dari halaman -->
@stack('scripts')
</body>
</html>