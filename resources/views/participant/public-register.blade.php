<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Peserta - {{ $agenda->nama_agenda }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/public-register.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
    <div class="govt-header">
        <div class="container text-center">
            <img src="{{ asset('storage/Pemkot.png') }}" alt="Pemkot Logo" style="width: 100px; height: auto; margin-bottom: 10px;">
            <h1><i class=></i>Dinas Komunikasi dan Informatika Kota Pontianak</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Agenda Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Informasi Agenda</h3>
                    </div>
                    <div class="card-body">
                        <div class="agenda-info">
                            <h4 class="agenda-title" id="agendaTitle">{{ $agenda->nama_agenda }}</h4>
                            <div class="agenda-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <span><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agenda Selection -->
                        <div class="form-group mt-4">
                            <label class="form-label">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Pilih Agenda Lain pada Tanggal yang Sama
                            </label>
                            <select class="form-select" id="agenda_id" name="agenda_id">
                                @foreach($agendasOnSameDate as $agendaItem)
                                    <option value="{{ $agendaItem->id }}" {{ $agendaItem->id == $agenda->id ? 'selected' : '' }}>
                                        {{ $agendaItem->nama_agenda }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Registration Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-plus me-2"></i>Formulir Pendaftaran</h3>
                    </div>
                    <div class="card-body">
                        <form id="registrationForm" method="POST" action="{{ route('agenda.register') }}">
                            @csrf
                            <input type="hidden" name="agenda_id" id="selected_agenda_id" value="{{ $agenda->id }}">
                            
                            <!-- Personal Data Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user-circle"></i>
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nama" name="nama" required 
                                               placeholder="Masukkan nama lengkap Anda">
                                        <div class="error-message" id="nama-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-venus-mars"></i>
                                            Jenis Kelamin <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <div class="error-message" id="gender-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-briefcase"></i>
                                            Jabatan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" required
                                               placeholder="Masukkan jabatan Anda">
                                        <div class="error-message" id="jabatan-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-phone-alt"></i>
                                            Nomor HP <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" required
                                               placeholder="Contoh: 081234567890"
                                               pattern="[0-9]{10,13}">
                                        <div class="error-message" id="no_hp-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-building"></i>
                                            Dinas Asal <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="dinas_id" name="dinas_id" required>
                                            <option value="">Pilih Dinas...</option>
                                            @foreach($dinas as $dinasItem)
                                                <option value="{{ $dinasItem->dinas_id }}">{{ $dinasItem->nama_dinas }}</option>
                                            @endforeach
                                        </select>
                                        <div class="error-message" accesskeyid="dinas_id-error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="signature-section">
                                <label class="form-label">
                                    <i class="fas fa-signature"></i>
                                    Tanda Tangan Digital <span class="text-danger">*</span>
                                </label>
                                <small class="text-muted d-block mb-2">Gunakan mouse atau jari untuk membuat tanda tangan di area berikut</small>
                                
                                <div class="signature-pad-container">
                                    <canvas id="signatureCanvas" width="400" height="200" style="border: 1px solid #ddd; background: white;"></canvas>
                                    <div class="signature-placeholder">
                                        <i class="fas fa-pen"></i>
                                        <p>Klik dan tarik untuk membuat tanda tangan</p>
                                    </div>
                                </div>
                                
                                <div class="signature-actions">
                                    <button type="button" class="btn btn-outline" id="clearSignature">
                                        <i class="fas fa-eraser me-1"></i>Hapus Tanda Tangan
                                    </button>
                                    <span class="text-muted">* Tanda tangan wajib diisi</span>
                                </div>
                                
                                <input type="hidden" name="signature" id="signatureInput" required>
                                <div class="error-message" id="signature-error"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Daftar Sekarang
                                </button>
                                <p class="text-muted mt-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pastikan semua data yang diisi sudah benar sebelum mendaftar
                                </p>
                            </div>
                        </form>

                        <!-- Success Message -->
                        <div id="successMessage" class="alert alert-success mt-4" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 fa-2x"></i>
                                <div>
                                    <h5 class="mb-2">Pendaftaran Berhasil!</h5>
                                    <p class="mb-2">Data Anda telah berhasil disimpan. Terima kasih telah mendaftar.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-error mt-4" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-3 fa-2x"></i>
                                <div>
                                    <h5 class="mb-1">Terjadi Kesalahan</h5>
                                    <p class="mb-0" id="errorText"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-white mt-5">
        <div class="container">
            <p>&copy; 2025 Copyright Dinas Komunikasi dan Informatika Kota Pontianak.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('js/signature-pad.js') }}"></script>
    <script src="{{ asset('js/public-register.js') }}"></script>
</body>
</html>
