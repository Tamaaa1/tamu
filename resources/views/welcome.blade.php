<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Agenda - TAMU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .admin-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
            </style>
    </head>
<body>
    <!-- Admin Login Link -->
    <div class="admin-link">
        <a href="{{ route('login') }}" class="btn btn-outline-light">
            <i class="fas fa-user-shield me-2"></i>Admin Login
        </a>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-calendar-check me-3"></i>
                Sistem Manajemen Agenda TAMU
            </h1>
            <p class="lead mb-5">
                Platform digital untuk mengelola agenda dan pendaftaran peserta dengan mudah dan efisien
            </p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Untuk Peserta Agenda
                        </h6>
                        <p class="mb-2">Klik link agenda yang telah dibagikan oleh admin untuk melakukan pendaftaran</p>
                        <p class="mb-0">
                            <strong>Fitur:</strong> Pendaftaran online, tanda tangan digital, dan QR Code otomatis
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Fitur Utama</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-calendar-plus fa-2x"></i>
                            </div>
                            <h5 class="card-title">Manajemen Agenda</h5>
                            <p class="card-text">Buat dan kelola agenda dengan mudah. Pilih dinas, set tanggal, dan generate link acara.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-plus fa-2x"></i>
                            </div>
                            <h5 class="card-title">Pendaftaran Peserta</h5>
                            <p class="card-text">Form pendaftaran online tanpa login. Peserta bisa daftar langsung dengan data lengkap.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-qrcode fa-2x"></i>
                            </div>
                            <h5 class="card-title">QR Code & TTD</h5>
                            <p class="card-text">Tanda tangan digital dan QR Code otomatis untuk setiap peserta yang mendaftar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Cara Kerja</h2>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fw-bold">1</span>
                    </div>
                    <h6>Admin Buat Agenda</h6>
                    <p class="small">Admin login dan buat agenda baru dengan pilih dinas</p>
                </div>
                
                <div class="col-md-3 text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fw-bold">2</span>
                    </div>
                    <h6>Share Link Agenda</h6>
                    <p class="small">Link agenda dibagikan ke peserta yang akan hadir</p>
                </div>
                
                <div class="col-md-3 text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fw-bold">3</span>
                    </div>
                    <h6>Peserta Daftar</h6>
                    <p class="small">Peserta klik link dan isi form pendaftaran</p>
        </div>

                <div class="col-md-3 text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="fw-bold">4</span>
                    </div>
                    <h6>QR Code & Notif</h6>
                    <p class="small">Sistem generate QR Code dan notif sukses</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Sistem Manajemen Agenda TAMU. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
