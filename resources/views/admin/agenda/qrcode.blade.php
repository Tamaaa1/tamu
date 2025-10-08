@extends('admin.layouts.app')

@section('title', 'QR Code Agenda - ' . $agenda->nama_agenda)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">QR Code Agenda</h1>
    <div>
        <a href="{{ route('admin.agenda.show', $agenda) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Detail
        </a>
        <a href="{{ route('admin.agenda.export-qrcode-pdf', $agenda) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Unduh & Cetak
        </a>
    </div>
</div>

<!-- QR Code Card -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold text-white">QR Code untuk Agenda: {{ $agenda->nama_agenda }}</h6>
            </div>
            <div class="card-body text-center">
                <!-- Agenda Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Nama Agenda</h5>
                        <p class="text-muted">{{ $agenda->nama_agenda }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Tanggal Agenda</h5>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <!-- QR Code Display -->
                <div class="mb-4">
                    <h5 class="font-weight-bold mb-3">Scan QR Code untuk Pendaftaran</h5>
                    <div class="d-flex justify-content-center">
                        <div style="border: 2px solid #007bff; padding: 20px; border-radius: 10px; background: white;">
                            {!! $qrCode !!}
                        </div>
                    </div>
                </div>
                
                <!-- URL Information -->
                <div class="mb-4">
                    <h5 class="font-weight-bold">Link Pendaftaran</h5>
                    <div class="input-group">
                        <input type="text" class="form-control" id="publicUrl" value="{{ $publicUrl }}" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="button" id="copyButton" onclick="copyToClipboard()">
                                <i class="fas fa-copy"></i> Salin
                            </button>
                            <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-success">
                                <i class="fas fa-external-link-alt"></i> Buka
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Dinas Information -->
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Instansi</h5>
                        <p class="text-muted">
                            <span class="badge badge-info" style="word-wrap: break-word; white-space: normal; max-width: 300px;">
                                {{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Koordinator</h5>
                        <p class="text-muted">{{ $agenda->nama_koordinator }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyToClipboard() {
    const urlInput = document.getElementById('publicUrl');
    const copyButton = document.getElementById('copyButton');
    const textToCopy = urlInput.value;

    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showSuccess();
        }).catch(function(err) {
            console.error('Clipboard API failed: ', err);
            fallbackCopy(textToCopy);
        });
    } else {
        fallbackCopy(textToCopy);
    }

    function fallbackCopy(text) {
        // Create a temporary textarea for reliable copying
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showSuccess();
            } else {
                throw new Error('execCommand failed');
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            alert("Gagal menyalin link. Silakan salin manual dari kotak teks.");
        } finally {
            document.body.removeChild(textArea);
        }
    }

    function showSuccess() {
        // Change button text temporarily
        const originalText = copyButton.innerHTML;
        copyButton.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        copyButton.classList.remove('btn-outline-primary');
        copyButton.classList.add('btn-success');

        // Reset button after 2 seconds
        setTimeout(function() {
            copyButton.innerHTML = originalText;
            copyButton.classList.remove('btn-success');
            copyButton.classList.add('btn-outline-primary');
        }, 2000);
    }
}
</script>
@endpush
