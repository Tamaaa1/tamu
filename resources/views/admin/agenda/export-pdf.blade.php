<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Agenda - {{ $agenda->nama_agenda }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c5282;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #4a5568;
            margin: 5px 0 0 0;
            font-size: 18px;
        }
        .content {
            margin: 20px 0;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #2d3748;
        }
        .info-value {
            flex: 1;
            color: #4a5568;
        }
        .qrcode-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #2c5282;
            border-radius: 10px;
        }
        .qrcode-title {
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .url-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 5px;
            border-left: 4px solid #2c5282;
        }
        .url-label {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }
        .url-value {
            color: #2b6cb0;
            word-break: break-all;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #718096;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .badge {
            background-color: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="header">
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
                    <span class="badge">{{ $agenda->masterDinas->nama_dinas ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Koordinator:</div>
                <div class="info-value">{{ $agenda->nama_koordinator }}</div>
            </div>
        </div>

        <div class="qrcode-container">
            <div class="qrcode-title">SCAN QR CODE UNTUK PENDAFTARAN</div>
            <img src="{{ $qrCodeBase64 }}" alt="QR Code Agenda" style="width: 200px; height: 200px;">
        </div>

        <div class="url-section">
            <div class="url-label">Link Pendaftaran:</div>
            <div class="url-value">{{ $publicUrl }}</div>
        </div>
    </div>

    <div class="footer">
        <p>Dibuat pada: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Sistem Manajemen Agenda</p>
    </div>
</body>
</html>
