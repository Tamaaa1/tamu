<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ekspor PDF Peserta</title>
    <style>
        body {
<<<<<<< HEAD
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .kop-wrapper { width: 100%; margin-bottom: 10px; }
        /* Gunakan layout tanpa kotak: logo kiri, teks kanan */
        .kop { display: table; width: 100%; }
        .kop-left { display: table-cell; width: 90px; vertical-align: middle; }
        .kop-right { display: table-cell; vertical-align: middle; text-align: center; }
        .kop-text .title-1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text .title-2 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text .address {
            font-size: 11px;
            margin-top: 4px;
        }
        .kop-divider {
            border: 0;
            border-top: 2px solid #000;
            margin: 6px 0 12px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .no-wrap {
            white-space: nowrap;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        img { max-width: 100%; height: auto; }
        .no-signature {
            color: #999;
            font-style: italic;
            font-size: 10px;
=======
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            color: #333;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #5e91cc;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        img {
            max-width: 80px;
            max-height: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .no-signature {
            color: #dc3545;
            font-style: italic;
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
        }
    </style>
</head>
<body>
<<<<<<< HEAD
    <div class="kop-wrapper">
        <div class="kop">
            <div class="kop-left">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo Pemkot" style="width:80px;">
                @endif
            </div>
            <div class="kop-right">
                <div class="kop-text">
                    <div class="title-1">Pemerintah Kota Pontianak</div>
                    <div class="title-2">{{ $agendaFilter ? $agendaFilter->masterDinas->nama_dinas : 'Dinas Komunikasi dan Informatika' }}</div>
                    <div class="address">
                        {{ $agendaFilter ? $agendaFilter->masterDinas->alamat : 'Jalan Rahadi Osman No.3 Telp. (0561) 733041 Fax (0561) Pontianak 78111' }}<br>
                        Website: diskominfo.pontianak.go.id • Email: {{ $agendaFilter ? $agendaFilter->masterDinas->email : 'diskominfo@pontianak.go.id' }}
                    </div>
                </div>
            </div>
        </div>
        <hr class="kop-divider">
    </div>

    <div style="text-align:center; margin-bottom:8px;">
        <div style="font-weight:bold; font-size:14px;">Daftar Hadir</div>
    </div>
    <table style="width:100%; border-collapse:collapse; border:none; margin-bottom:6px;">
        <tr>
            <td style="width:110px; font-size:12px; border:none; padding:0;">Nama Agenda</td>
            <td style="width:10px; font-size:12px; border:none; padding:0;">:</td>
            <td style="font-size:12px; border:none; padding:0;">{{ $agendaFilter ? $agendaFilter->nama_agenda : 'Semua Agenda' }}</td>
        </tr>
        @if($agendaFilter)
        <tr>
            <td style="font-size:12px; border:none; padding:0;">Tanggal</td>
            <td style="font-size:12px; border:none; padding:0;">:</td>
            <td style="font-size:12px; border:none; padding:0;">{{ \Carbon\Carbon::parse($agendaFilter->tanggal_agenda)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="font-size:12px; border:none; padding:0;">Tempat</td>
            <td style="font-size:12px; border:none; padding:0;">:</td>
            <td style="font-size:12px; border:none; padding:0;">{{ $agendaFilter->tempat ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-size:12px; border:none; padding:0;">Waktu</td>
            <td style="font-size:12px; border:none; padding:0;">:</td>
            <td style="font-size:12px; border:none; padding:0;">{{ $agendaFilter->waktu ?? 'N/A' }}</td>
        </tr>
        @endif

    </table>
    <div style="font-size:10px; margin-top:2px;">Total Peserta: {{ count($participants) }}</div>

=======
    <div class="header" style="text-align: center; margin-bottom: 20px;">
        <div>
            <img src="{{ public_path('storage/Pemkot.png') }}" alt="Pemkot Logo" style="width: 80px; height: auto; margin-bottom: 10px;">
        </div>
        <h1>{{ $agendaFilter ? $agendaFilter->nama_agenda : 'Semua Agenda' }}</h1>
        <p><strong>Tanggal Dibuat:</strong> {{ $agendaFilter ? $agendaFilter->created_at->format('d/m/Y') : 'N/A' }}</p>
        <p><strong>Total Peserta:</strong> {{ count($participants) }}</p>
    </div>

>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
    <table>
        <thead>
            <tr>
                <th>No</th>
<<<<<<< HEAD
                <th class="no-wrap">Nama</th>
                <th>L/P</th>
                <th>Perangkat Daerah</th>
                <th class="no-wrap">Jabatan</th>
                <th>Telepon</th>
=======
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Perangkat Daerah</th>
                <th>Jabatan</th>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
<<<<<<< HEAD
                    <td class="no-wrap">{{ $participant->nama }}</td>
                    <td>{{ $participant->gender == 'Laki-laki' ? 'L' : ($participant->gender == 'Perempuan' ? 'P' : $participant->gender) }}</td>
                    <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                    <td class="no-wrap">{{ $participant->jabatan }}</td>
                    <td>{{ $participant->no_hp }}</td>
=======
                    <td>{{ $participant->nama }}</td>
                    <td>{{ $participant->gender}}</td>
                    <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                    <td>{{ $participant->jabatan }}</td>
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
                    <td>
                        @if($participant->gambar_ttd)
                            @php
                                $signaturePath = storage_path('app/private/' . $participant->gambar_ttd);
                                if (file_exists($signaturePath)) {
<<<<<<< HEAD
                                    // Encode gambar ke base64 untuk performa lebih baik (hindari I/O file)
                                    $imageData = file_get_contents($signaturePath);
                                    $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
                                    echo '<img src="' . $base64Image . '" alt="Tanda Tangan" style="max-width:60px;max-height:30px;">';
=======
                                    $imageData = base64_encode(file_get_contents($signaturePath));
                                    $src = 'data:image/png;base64,' . $imageData;
                                    echo '<img src="' . $src . '" alt="Tanda Tangan">';
>>>>>>> 284e251ce60564e812888c40ae43c01b7d4a7614
                                } else {
                                    echo '<span class="no-signature">File tidak ditemukan</span>';
                                }
                            @endphp
                        @else
                            <span class="no-signature">Tidak ada tanda tangan</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
