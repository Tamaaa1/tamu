<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Agenda TAMU</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Admin Login Button -->
    <a href="{{ route('login') }}" class="admin-btn">
        <i class="fas fa-user-shield me-2"></i>Admin Login
    </a>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">            
            <h1 class="hero-title">
                <i class="fas fa-calendar-check me-3"></i>
                Sistem Manajemen Agenda TAMU
            </h1>
            <p class="hero-subtitle">
                Platform digital modern untuk mengelola agenda dan pendaftaran peserta dengan mudah dan efisien
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-1">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            
            <div class="row g-4">
                <!-- Agenda Management -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h3 class="feature-title">Manajemen Agenda</h3>
                        <p class="feature-description">
                            Buat dan kelola agenda dengan mudah. Pilih dinas, set tanggal, dan generate link acara secara otomatis.
                        </p>
                    </div>
                </div>
                
                <!-- Participant Registration -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="feature-title">Pendaftaran Peserta</h3>
                        <p class="feature-description">
                            Form pendaftaran online tanpa login. Peserta bisa daftar langsung dengan data lengkap dan tanda tangan digital.
                        </p>
                    </div>
                </div>
                
                <!-- QR Code & Digital Signature -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h3 class="feature-title">QR Code & TTD Digital</h3>
                        <p class="feature-description">
                            Tanda tangan digital dan QR Code otomatis untuk setiap peserta. Export PDF dengan QR code yang dapat discan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Cara Kerja Sistem</h2>
            
            <div class="row g-4 text-center text-dark">
                <div class="col-md-3">
                    <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <span class="fw-bold">1</span>
                    </div>
                    <h5>Admin Buat Agenda</h5>
                    <p class="small">Admin login dan buat agenda baru dengan pilih dinas</p>
                </div>
                
                <div class="col-md-3">
                    <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <span class="fw-bold">2</span>
                    </div>
                    <h5>Share Link Agenda</h5>
                    <p class="small">Link agenda atau Qr code dibagikan ke peserta yang akan hadir</p>
                </div>
                
                <div class="col-md-3">
                    <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <span class="fw-bold">3</span>
                    </div>
                    <h5>Peserta Daftar</h5>
                    <p class="small">Peserta klik link atau scan QR code dan isi form pendaftaran online</p>
                </div>

                <div class="col-md-3">
                    <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        <span class="fw-bold">4</span>
                    </div>
                    <h5>QR Code & Notif</h5>
                    <p class="small">Sistem generate QR Code dan notif sukses</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sistem Manajemen Agenda TAMU. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
