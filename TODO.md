# TODO: Link Agenda ke Halaman Pendaftaran Peserta

## Tujuan
Membuat link di kolom "Link Acara" pada halaman agenda admin yang mengarah ke halaman pendaftaran peserta dengan agenda spesifik, termasuk fitur dropdown untuk multiple agenda dalam 1 hari.

## Langkah-langkah:
1. [x] Menambahkan route baru di routes/web.php
2. [x] Menambahkan method baru di AgendaController.php
3. [x] Mengubah link di resources/views/admin/agenda/index.blade.php
4. [x] Memperbaiki path view di controller
5. [x] Menambahkan dropdown pemilihan agenda di halaman pendaftaran
6. [x] Memperbarui controller untuk mengambil semua agenda pada tanggal yang sama

## Progress:
- Semua langkah telah selesai
- Route: `/agenda/{agenda}/register` telah ditambahkan
- Method `showPublicAgenda` telah ditambahkan di controller dengan logika dropdown
- Link di halaman agenda telah diubah untuk mengarah ke route baru
- Path view telah diperbaiki dari `agenda.public-register` menjadi `admin.agenda.public-register`
- Dropdown agenda ditambahkan untuk menangani multiple agenda dalam 1 hari
- Controller sekarang mengirim data `$agendasOnSameDate` untuk dropdown
