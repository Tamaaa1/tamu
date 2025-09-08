# Sistem Manajemen Agenda - Dokumentasi

## Deskripsi
Sistem Manajemen Agenda ini adalah aplikasi berbasis Laravel 12 yang dirancang untuk memudahkan pengelolaan agenda acara, pendaftaran peserta, dan administrasi terkait. Sistem ini mendukung fitur digital signature, QR code, serta panel admin yang lengkap untuk manajemen agenda, peserta, dan user.

## Fitur Utama

### Panel Admin
- Dashboard untuk monitoring sistem secara keseluruhan.
- CRUD agenda dengan detail acara lengkap.
- Manajemen peserta yang mendaftar pada agenda.
- Database master dinas/instansi.
- Manajemen user dengan kontrol akses berbasis role.
- Export data peserta dan agenda ke format PDF dan Excel.
- Generate QR Code untuk akses cepat ke pendaftaran agenda.
- Toggle status aktif/nonaktif link pendaftaran agenda.

### Pendaftaran Publik
- Form pendaftaran yang user-friendly dan responsif.
- Validasi data peserta termasuk tanda tangan digital.
- Proteksi rate limiting untuk mencegah spam.
- Pendaftaran berdasarkan token unik yang terhubung dengan agenda.
- QR Code yang di-generate untuk data peserta.

### Keamanan dan Performa
- Sistem autentikasi yang aman.
- Middleware untuk kontrol akses admin.
- Caching data untuk meningkatkan performa.
- Indexing database untuk query yang cepat.

## Teknologi yang Digunakan

- Laravel 12 (PHP 8.2+)
- MySQL 8.0+
- Blade Templates, Tailwind CSS, Alpine.js, Vite
- DomPDF untuk generate PDF
- SimpleQRCode untuk generate QR Code
- Maatwebsite Excel untuk export Excel
- Signature Pad untuk tanda tangan digital

## Instalasi

1. Clone repository:
   ```
   git clone https://github.com/Tamaaaaa1/tamu.git
   cd sistem-agenda
   ```

2. Install dependencies PHP:
   ```
   composer install
   ```

3. Install dependencies JavaScript:
   ```
   npm install
   ```

4. Konfigurasi environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. Konfigurasi database di file `.env`.

6. Jalankan migration dan seeder:
   ```
   php artisan migrate
   php artisan db:seed
   ```

7. Build assets:
   ```
   npm run build
   # atau untuk development
   npm run dev
   ```

8. Jalankan server:
   ```
   php artisan serve
   ```

## Penggunaan

- Halaman publik pendaftaran agenda: `http://localhost:8000/agenda`
- Panel admin: `http://localhost:8000`

### Admin
- Login ke panel admin.
- Kelola agenda, peserta, master dinas, dan user.
- Export data dan generate QR Code.

### Peserta
- Akses link atau QR Code agenda.
- Isi form pendaftaran dengan data lengkap dan tanda tangan digital.
- Konfirmasi pendaftaran.

## Struktur Routes

- Public routes untuk pendaftaran dan tampilan agenda.
- Admin routes dengan middleware autentikasi dan admin.
- Fitur rate limiting pada endpoint pendaftaran publik.
