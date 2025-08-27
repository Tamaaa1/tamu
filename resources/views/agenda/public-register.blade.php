<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Peserta - {{ $agenda->nama_agenda }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .signature-pad {
            border: 2px dashed #ccc;
            border-radius: 8px;
            background: #f9f9f9;
            cursor: crosshair;
        }
        .signature-pad canvas {
            border-radius: 6px;
            width: 100%;
            height: 200px;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .success-message {
            color: #198754;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Pendaftaran Peserta Agenda
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Agenda Info -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Informasi Agenda:</h6>
                            <p class="mb-1"><strong>Nama Agenda:</strong> {{ $agenda->nama_agenda }}</p>
                            <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d F Y') }}</p>
                            <p class="mb-0"><strong>Link Acara:</strong> <a href="{{ $agenda->link_acara }}" target="_blank">{{ $agenda->link_acara }}</a></p>
                        </div>

                        <!-- Registration Form -->
                        <form id="registrationForm" method="POST" action="{{ route('agenda.register') }}">
                            @csrf
                            <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                    <div class="error-message" id="nama-error"></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                                    <div class="error-message" id="jabatan-error"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                                    <div class="error-message" id="no_hp-error"></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="dinas_id" class="form-label">Dinas Asal <span class="text-danger">*</span></label>
                                    <select class="form-select" id="dinas_id" name="dinas_id" required>
                                        <option value="">Pilih Dinas</option>
                                        @foreach($dinas as $dinasItem)
                                            <option value="{{ $dinasItem->dinas_id }}">{{ $dinasItem->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="dinas_id-error"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Tanda Tangan <span class="text-danger">*</span></label>
                                <div class="signature-pad" id="signaturePad">
                                    <canvas id="signatureCanvas"></canvas>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-secondary"id="clearSignature">
                                        <i class="fas fa-eraser me-1"></i>Hapus Tanda Tangan
                                    </button>
                                </div>
                                <input type="hidden" name="signature" id="signatureInput" required>
                                <div class="error-message" id="signature-error"></div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Daftar Sekarang
                                </button>
                            </div>
                        </form>

                        <!-- Success Message -->
                        <div id="successMessage" class="alert alert-success mt-3" style="display: none;">
                            <h5 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>Pendaftaran Berhasil!
                            </h5>
                            <p class="mb-2">Data Anda telah berhasil disimpan.</p>
                            <div id="qrCodeContainer" class="text-center mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        // Inisialisasi Signature Pad
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas, {
            minWidth: 1,
            maxWidth: 3,
            penColor: "rgb(0, 0, 0)"
        });

        // Resize canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Clear canvas on resize
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Clear signature
        document.getElementById('clearSignature').addEventListener('click', function() {
            signaturePad.clear();
            document.getElementById('signatureInput').value = '';
        });

        // Update signature input on end of drawing
        signaturePad.addEventListener('endStroke', () => {
            if (!signaturePad.isEmpty()) {
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signatureInput').value = signatureData;
            }
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            // Clear
            clearErrors();
            
            // Validasi Signature
            if (signaturePad.isEmpty()) {
                showError('signature', 'Tanda tangan harus diisi');
                return;
            }

            // Update signature input if not already set
            if (!document.getElementById('signatureInput').value) {
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signatureInput').value = signatureData;
            }

            // Submit form
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            // Debug: Log CSRF token untuk memastikan ada
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
            console.log('CSRF Token:', csrfToken);
            
            fetch('/agenda/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
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
                } else {
                    showErrors(data.errors);
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                if (error.errors) {
                    showErrors(error.errors);
                } else if (error.message) {
                    showError('general', 'Error: ' + error.message);
                } else {
                    showError('general', 'Terjadi kesalahan (Status: 419 - CSRF Token Mismatch). Silakan refresh halaman dan coba lagi.');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        function clearErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => element.textContent = '');
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

        function showSuccess(message, qrCodeBase64) {
            document.getElementById('successMessage').style.display = 'block';
            
            // Create img element for base64 QR code
            const img = document.createElement('img');
            img.src = qrCodeBase64;
            img.alt = 'QR Code Pendaftaran';
            img.style.maxWidth = '200px';
            img.style.height = 'auto';
            
            document.getElementById('qrCodeContainer').innerHTML = '';
            document.getElementById('qrCodeContainer').appendChild(img);
            
            // Scroll to success message
            document.getElementById('successMessage').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
