<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ekspor PDF Peserta</title>
    <style>
        body {
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
        }
    </style>
</head>
<body>
    <div class="header" style="text-align: center; margin-bottom: 20px;">
        <div>
            <img src="{{ public_path('storage/Pemkot.png') }}" alt="Pemkot Logo" style="width: 80px; height: auto; margin-bottom: 10px;">
        </div>
        <h1>{{ $agendaFilter ? $agendaFilter->nama_agenda : 'Semua Agenda' }}</h1>
        <p><strong>Tanggal Dibuat:</strong> {{ $agendaFilter ? $agendaFilter->created_at->format('d/m/Y') : 'N/A' }}</p>
        <p><strong>Total Peserta:</strong> {{ count($participants) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Perangkat Daerah</th>
                <th>Jabatan</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->nama }}</td>
                    <td>{{ $participant->gender}}</td>
                    <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                    <td>{{ $participant->jabatan }}</td>
                    <td>
                        @if($participant->gambar_ttd)
                            @php
                                $signaturePath = storage_path('app/private/' . $participant->gambar_ttd);
                                if (file_exists($signaturePath)) {
                                    $imageData = base64_encode(file_get_contents($signaturePath));
                                    $src = 'data:image/png;base64,' . $imageData;
                                    echo '<img src="' . $src . '" alt="Tanda Tangan">';
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
