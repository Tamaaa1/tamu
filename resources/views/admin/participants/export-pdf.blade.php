<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ekspor PDF Peserta</title>
    <style>
        body {
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
        }
    </style>
</head>
<body>
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

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th class="no-wrap">Nama</th>
                <th>L/P</th>
                <th>Perangkat Daerah</th>
                <th class="no-wrap">Jabatan</th>
                <th>Telepon</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="no-wrap">{{ $participant->nama }}</td>
                    <td>{{ $participant->gender == 'Laki-laki' ? 'L' : ($participant->gender == 'Perempuan' ? 'P' : $participant->gender) }}</td>
                    <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                    <td class="no-wrap">{{ $participant->jabatan }}</td>
                    <td>{{ $participant->no_hp }}</td>
                    <td>
                        @if($participant->gambar_ttd)
                            @php
                                $signaturePath = storage_path('app/private/' . $participant->gambar_ttd);
                                if (file_exists($signaturePath)) {
                                    // Encode gambar ke base64 untuk performa lebih baik (hindari I/O file)
                                    $imageData = file_get_contents($signaturePath);
                                    $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
                                    echo '<img src="' . $base64Image . '" alt="Tanda Tangan" style="max-width:60px;max-height:30px;">';
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
