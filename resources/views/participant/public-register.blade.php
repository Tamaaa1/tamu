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
        <div class="container">
            <div class="text-center">
                <h1><i class="fas fa-landmark me-2"></i>Sistem Pendaftaran Peserta</h1>
                <p>Dinas Komunikasi dan Informatika</p>
            </div>
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
                                            <i class="fas fa-briefcase"></i>
                                            Jabatan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" required 
                                               placeholder="Masukkan jabatan Anda">
                                        <div class="error-message" id="jabatan-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                        <div class="error-message" id="dinas_id-error"></div>
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
                                
                                <div class="signature-pad" id="signaturePad">
                                    <canvas id="signatureCanvas"></canvas>
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
            <p>&copy; 2025 Dinas Komunikasi dan Informatika. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('js/signature-pad.js') }}"></script>
    <script>

        // Form submission handling
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            // Clear errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.getElementById('errorMessage').style.display = 'none';
            
            // Validate signature menggunakan signaturePadManager
            const signatureError = window.signaturePadManager.validate();
            if (signatureError) {
                showError('signature', signatureError);
                return;
            }

            // Update signature input
            if (!document.getElementById('signatureInput').value) {
                const signatureData = window.signaturePadManager.toDataURL();
                document.getElementById('signatureInput').value = signatureData;
            }

            // Submit form
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            fetch('/agenda/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccess(data.message, data.qr_code);
                    document.getElementById('registrationForm').reset();
                    signaturePad.clear();
                    document.querySelector('.signature-placeholder').style.display = 'flex';
                } else {
                    showErrors(data.errors);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.errors) {
                    showErrors(error.errors);
                } else {
                    showError('general', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-loading');
                submitBtn.innerHTML = originalText;
            });
        });

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.getElementById('errorMessage').style.display = 'none';
        }

        function showError(field, message) {
            const errorElement = document.getElementById(field + '-error');
            if (errorElement) {
                errorElement.textContent = message;
            }
        }

        function showErrors(errors) {
            Object.keys(errors).forEach(field => {
                showError(field, errors[field][0]);
            });
        }

        function showSuccess(message) {
            const successElement = document.getElementById('successMessage');
            successElement.style.display = 'block';
            
            // Hapus QR code container jika ada
            const qrContainer = document.getElementById('qrCodeContainer');
            if (qrContainer) {
                qrContainer.style.display = 'none';
            }
            
            successElement.scrollIntoView({ behavior: 'smooth' });
        }

        // Agenda selection change
        document.getElementById('agenda_id').addEventListener('change', function() {
            const selectedAgendaId = this.value;
            document.getElementById('selected_agenda_id').value = selectedAgendaId;
            
            // Update agenda title
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('agendaTitle').innerText = selectedOption.text;
        });

        // Input validation
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', function() {
                const errorElement = document.getElementById(this.name + '-error');
                if (errorElement) {
                    errorElement.textContent = '';
                }
            });
        });

        // Copy to clipboard function
        function copyToClipboard(text, event) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            });
        }
    </script>
</body>
</html>
