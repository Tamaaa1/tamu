# Panduan Keamanan untuk Repository GitHub Sistem Manajemen Agenda

Dokumentasi ini menjelaskan aspek keamanan penting yang diterapkan dalam proyek Sistem Manajemen Agenda. Pastikan untuk mengikuti panduan ini agar aplikasi tetap aman dan sesuai standar keamanan terbaik.

---

## 1. Konfigurasi Environment

- Jangan pernah menyimpan file `.env` di repository publik.
- Pastikan variabel berikut diatur dengan benar di environment production:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `SESSION_SECURE_COOKIE=true`
  - `SESSION_HTTP_ONLY=true`
  - `SESSION_SAME_SITE=lax`
- Gunakan SSL/HTTPS untuk semua komunikasi.

## 2. Autentikasi dan Otorisasi

- Password disimpan menggunakan hashing yang aman (`bcrypt` via Laravel `Hash::make`).
- Semua route admin dilindungi middleware `auth` dan `admin`.
- Rate limiting diterapkan pada route login dan pendaftaran publik untuk mencegah brute force dan spam.
- CSRF protection aktif pada semua form menggunakan `@csrf` directive di Blade templates.

## 3. Validasi Input dan Upload File

- Semua input divalidasi secara ketat di controller.
- Upload tanda tangan digital hanya menerima file gambar dengan tipe `png`, `jpg`, `jpeg` dan ukuran maksimal 2MB.
- File tanda tangan disimpan dengan nama unik di direktori yang aman (`storage/app/public/tandatangan`).

## 4. Session dan Cookie

- Session menggunakan driver database untuk keamanan lebih baik.
- Session dienkripsi dan cookie diset dengan flag `Secure` dan `HttpOnly`.
- Session lifetime disesuaikan (default 2 jam).

## 5. Database

- Menggunakan prepared statements melalui Eloquent ORM untuk mencegah SQL Injection.
- Database user memiliki hak akses terbatas sesuai kebutuhan.
- Indexing pada tabel untuk performa dan keamanan query.

## 6. Logging dan Monitoring

- Aktivitas penting seperti login gagal, upload file, dan aktivitas admin dicatat di log.
- Log error dan exception dicatat untuk investigasi keamanan.

## 7. Backup dan Recovery

- Backup database dan file upload secara berkala.
- Simpan backup di lokasi terpisah dan aman.

## 8. Update dan Patch

- Framework Laravel dan dependencies selalu diperbarui ke versi terbaru.
- Pantau advisories keamanan dan segera lakukan patch jika ditemukan kerentanan.

## 9. Testing Keamanan

- Manual testing meliputi:
  - Uji rate limiting login dan pendaftaran.
  - Uji akses route admin tanpa autentikasi.
  - Uji upload file dengan tipe dan ukuran tidak valid.
  - Uji CSRF protection.
- Automated testing sudah tersedia di folder `tests/Feature` dan `tests/Unit`.

## 10. Respons Darurat

- Jika terjadi pelanggaran keamanan:
  - Segera matikan akses publik jika perlu.
  - Reset semua session pengguna.
  - Review log untuk investigasi.
  - Update kredensial yang terkompromi.
  - Deploy patch keamanan.
