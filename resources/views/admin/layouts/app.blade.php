<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - TAMU</title>

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])

    <!-- AOS Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">


    @stack('styles')
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sidebar-brand-text mx-3">TAMU Admin</div>
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
                <span>Dinas</span>
            </a>
        </li>

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
        <footer class="sticky-footer bg-gradient-primary text-white">
            <div class="container text-center">
                <span>© {{ date('Y') }} TAMU | testing</span>
            </div>
        </footer>
    </div>
</div>

<!-- Scroll Top -->
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>

@stack('scripts')
</body>
</html>
