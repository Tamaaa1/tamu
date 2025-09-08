# 🎯 Sistem Manajemen Agenda

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

> 🚀 **Sistem Manajemen Agenda Modern** - Platform lengkap untuk mengelola agenda acara, pendaftaran peserta, dan administrasi dengan fitur digital signature dan QR code.

## ✨ Fitur Utama

### 👨‍💼 Panel Admin
- 📊 **Dashboard** - Overview lengkap sistem
- 📅 **Manajemen Agenda** - CRUD agenda dengan detail acara
- 👥 **Manajemen Peserta** - Kelola pendaftaran peserta
- 🏢 **Master Dinas** - Database departemen/instansi
- 👤 **Manajemen User** - Kontrol akses admin

### 🌐 Pendaftaran Publik
- 📝 **Form Pendaftaran** - Interface user-friendly
- ✍️ **Digital Signature** - Tanda tangan elektronik
- 🔒 **Rate Limiting** - Proteksi dari spam
- 📱 **Responsive Design** - Mobile-friendly

### 📊 Laporan & Export
- 📄 **Export PDF** - Laporan agenda dan peserta
- 📊 **Export Excel** - Data dalam format spreadsheet
- 📱 **QR Code** - Generate QR untuk akses cepat
- 🔍 **Filtering** - Pencarian berdasarkan tanggal, bulan, tahun

### 🔐 Keamanan & Performa
- 🔐 **Authentication** - Sistem login aman
- 🛡️ **Middleware Admin** - Kontrol akses berbasis role
- ⚡ **Caching** - Optimasi performa
- 🗄️ **Database Indexing** - Query cepat

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - Framework PHP modern
- **PHP 8.2+** - Bahasa pemrograman
- **MySQL 8.0+** - Database relasional

### Frontend
- **Blade Templates** - Template engine Laravel
- **Tailwind CSS** - Framework CSS utility-first
- **Alpine.js** - JavaScript framework minimal
- **Vite** - Build tool modern

### Libraries & Packages
- **DomPDF** - Generate PDF
- **SimpleQRCode** - Generate QR Code
- **Maatwebsite Excel** - Export Excel
- **Signature Pad** - Digital signature

## 🚀 Instalasi

### Persyaratan Sistem
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL 8.0+
- Git

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/Tamaaaaa1/tamu.git
   cd sistem-agenda
   ```

2. **Install Dependencies PHP**
   ```bash
   composer install
   ```

3. **Install Dependencies JavaScript**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Konfigurasi Database**
   - Buat database MySQL baru
   - Update file `.env` dengan kredensial database

6. **Jalankan Migration & Seeder**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build Assets**
   ```bash
   npm run build
   # atau untuk development
   npm run dev
   ```

8. **Jalankan Server**
   ```bash
   php artisan serve
   ```

## 📖 Penggunaan

### Akses Sistem
- **Halaman Publik**: `http://localhost:8000/agenda`
- **Panel Admin**: `http://localhost:8000`

### Fitur Utama

#### Untuk Admin
1. **Login** ke panel admin
2. **Buat Agenda** baru dengan detail acara
3. **Kelola Peserta** yang mendaftar
4. **Generate QR Code** untuk akses cepat
5. **Export Data** dalam format PDF/Excel

#### Untuk Peserta
1. **Akses Link/QR code Agenda** yang diberikan
2. **Isi Form Pendaftaran** dengan data lengkap
3. **Tanda Tangan Digital** menggunakan signature pad
4. **Konfirmasi Pendaftaran**

## 🔧 Konfigurasi

### Environment Variables
```env
APP_NAME="Sistem Manajemen Agenda"
APP_ENV=local
APP_KEY=base64:key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_tamu
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### Queue & Background Jobs
```bash
# Jalankan queue worker
php artisan queue:work

# Jalankan scheduler
php artisan schedule:run
```

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test spesifik
php artisan test --filter=AuthTest
```

## 📚 API Documentation

Sistem ini menyediakan API endpoints untuk integrasi:

- `GET /api/agenda` - List agenda aktif
- `POST /api/agenda/register` - Pendaftaran peserta
- `GET /api/agenda/{id}/participants` - List peserta (admin only)

### Panduan Kontribusi
- Ikuti PSR-12 coding standard
- Tambahkan test untuk fitur baru
- Update dokumentasi jika diperlukan
- Pastikan semua test pass

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS
- [DomPDF](https://github.com/dompdf/dompdf) - PDF generation
- [SimpleQRCode](https://github.com/SimpleSoftwareIO/simple-qrcode) - QR Code library

[⬆️ Back to top](#-sistem-manajemen-agenda)

</div>
