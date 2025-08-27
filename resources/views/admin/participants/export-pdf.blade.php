<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ekspor PDF Peserta</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        img { max-width: 80px; max-height: 40px; }
    </style>
</head>
<body>
    <h1>Daftar Peserta</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Dinas</th>
                <th>Agenda</th>
                <th>Tanggal Daftar</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->nama }}</td>
                    <td>{{ $participant->jabatan }}</td>
                    <td>{{ $participant->masterDinas->nama_dinas ?? 'N/A' }}</td>
                    <td>{{ $participant->agenda->nama_agenda ?? 'N/A' }}</td>
                    <td>{{ $participant->created_at->format('d/m/Y') }}</td>
                    <td>
                        @if($participant->gambar_ttd)
                            @php
                                $signaturePath = storage_path('app/public/' . $participant->gambar_ttd);
                                if (file_exists($signaturePath)) {
                                    $imageData = base64_encode(file_get_contents($signaturePath));  
                                    $src = 'data:image/png;base64,' . $imageData;
                                    echo '<img src="' . $src . '" alt="Tanda Tangan" style="max-width: 80px; max-height: 40px;">';
                                } else {
                                    echo '<p style="color: red;">File tanda tangan tidak ditemukan</p>';
                                }
                            @endphp
                        @else
                            <p style="color: red;">Tidak ada tanda tangan untuk peserta ini.</p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
