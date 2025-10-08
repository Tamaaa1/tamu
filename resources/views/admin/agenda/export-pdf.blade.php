<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Agenda - {{ $agenda->nama_agenda }}</title>
</head>
<style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
    }

    .container {
        background: white;
        margin: 5mm;
        padding: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .header-banner {
        background: #1e3a8a;
        padding: 20px 30px;
        position: relative;
        overflow: hidden;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 15px;
        position: relative;
        z-index: 1;
    }

    .logo-wrapper {
        display: inline-block;
        background: white;
        padding: 12px;
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .header-title {
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .header-title h1 {
        color: white;
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .header-title h2 {
        color: rgba(255, 255, 255, 0.95);
        margin: 0;
        font-size: 18px;
        font-weight: normal;
    }

    .content {
        padding: 20px;
    }

    .info-card {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #3b82f6;
    }

    .info-row {
        display: flex;
        margin-bottom: 15px;
        align-items: flex-start;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-weight: bold;
        width: 130px;
        color: #2d3748;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .info-label::before {
        content: '●';
        color: #3b82f6;
        margin-right: 8px;
        font-size: 10px;
    }

    .info-value {
        flex: 1;
        color: #4a5568;
        font-size: 14px;
        line-height: 1.5;
    }

    .qrcode-container {
        text-align: center;
        margin: 20px 0;
        padding: 20px;
        background: #1e3a8a;
        border-radius: 10px;
    }

    .qrcode-inner {
        background: white;
        display: inline-block;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        position: relative;
        z-index: 1;
    }

    .qrcode-title {
        font-weight: bold;
        color: white;
        margin-bottom: 20px;
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .qr-instruction {
        color: rgba(255, 255, 255, 0.95);
        font-size: 13px;
        margin-top: 15px;
        font-style: italic;
    }

    .badge {
        background: #1e3a8a;
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 12px;
        display: inline-block;
        font-weight: 600;
    }

    .footer {
        background: #f8fafc;
        margin: 0;
        padding: 15px 20px;
        text-align: center;
        border-top: 2px solid #3b82f6;
    }

    .footer p {
        color: #4a5568;
        font-size: 11px;
        margin: 5px 0;
        line-height: 1.6;
    }

    .footer-highlight {
        color: #1e3a8a;
        font-weight: bold;
    }
</style>
<body>
<<<<<<< HEAD
    <div class="container">
        <div class="header-banner">
            <div class="header-title">
                <h1>QR Code Agenda</h1>
                <h2>{{ $agenda->nama_agenda }}</h2>
            </div>
        </div>

        <div class="content">
            <div class="info-card">
                <div class="info-row">
                    <div class="info-label">Nama Agenda</div>
                    <div class="info-value">{{ $agenda->nama_agenda }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Instansi</div>
                    <div class="info-value">
                        <span class="badge">{{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}</span>
                    </div>
=======
    <div class="header">
        <div class="text-center mb-3">
            <img src="{{ public_path('storage/Pemkot.png') }}" alt="Pemkot Logo" style="width: 80px; height: auto;">
        </div>
        <h1>QR CODE AGENDA</h1>
        <h2>{{ $agenda->nama_agenda }}</h2>
    </div>

    <div class="content">
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Nama Agenda:</div>
                <div class="info-value">{{ $agenda->nama_agenda }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dinas:</div>
                <div class="info-value">
                    <span class="badge" style="word-wrap: break-word; white-space: normal; max-width: 300px;">{{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}</span>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
                </div>
            </div>

            <div class="qrcode-container">
                <div class="qrcode-title">Scan QR Code untuk Absensi</div>
                <div class="qrcode-inner">
                    <img src="{{ $qrCodeBase64 }}" alt="QR Code Agenda" style="width: 290px; height: 290px; display: block;">
                </div>
                <div class="qr-instruction">Arahkan kamera smartphone Anda ke QR Code di atas</div>
            </div>
        </div>

        <div class="footer">
            <p>Dibuat pada: <span class="footer-highlight">{{ now()->format('d/m/Y') }}</span></p>
            <p><strong>Sistem Manajemen Agenda</strong></p>
            <p>&copy; {{ now()->format('Y') }} Dinas Komunikasi dan Informatika Kota Pontianak</p>
        </div>
    </div>
</body>
</html>