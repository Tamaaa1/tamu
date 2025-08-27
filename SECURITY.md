# Panduan Keamanan Aplikasi TAMU

## Konfigurasi Keamanan Production

### 1. Environment Variables untuk Production

Pastikan konfigurasi berikut di file `.env` untuk environment production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 2. Rate Limiting

Aplikasi sudah dilengkapi dengan rate limiting untuk login:
- Maksimal 5 attempt per menit untuk login
- Mencegah brute force attacks

### 3. Authentication & Authorization

**Fitur Keamanan yang Sudah Diimplementasikan:**
- ✅ Password hashing dengan bcrypt
- ✅ CSRF protection untuk semua form
- ✅ Session management yang aman
- ✅ Role-based access control (hanya admin yang bisa login)
- ✅ Input validation komprehensif

### 4. File Upload Security

**Signature Upload:**
- Validasi file type (png, jpg, jpeg)
- Maximum file size: 2MB
- File disimpan dengan nama unique
- Directory: `storage/app/public/tandatangan`

### 5. Database Security

**Best Practices:**
- Gunakan prepared statements (Eloquent sudah handle ini)
- Validasi semua input sebelum ke database
- Gunakan database user dengan privileges terbatas

### 6. Session Security

**Konfigurasi yang Disarankan:**
```env
SESSION_DRIVER=database  # Lebih aman daripada file
SESSION_LIFETIME=120     # 2 jam
SESSION_ENCRYPT=true     # Encrypt session data
SESSION_SECURE_COOKIE=true # Hanya HTTPS
```

### 7. HTTPS Configuration

**Wajib untuk Production:**
- Gunakan SSL certificate
- Set `APP_URL` dengan https://
- Enable HSTS header jika memungkinkan

### 8. Regular Security Updates

**Yang Perlu Diperhatikan:**
- Update Laravel framework secara berkala
- Update dependencies dengan `composer update`
- Monitor security advisories

### 9. Backup Strategy

**Rekomendasi Backup:**
- Backup database regularly
- Backup file uploads (signatures)
- Simpan backup di lokasi terpisah

### 10. Monitoring & Logging

**Yang Harus Dimonitor:**
- Failed login attempts
- File upload activities
- Admin activities
- Error logs

## Cara Testing Keamanan

### Manual Testing:
1. Test login dengan credential salah (harus ada rate limiting)
2. Test akses admin routes tanpa login (harus redirect ke login)
3. Test file upload dengan file tidak valid
4. Test CSRF protection dengan request tanpa token

### Automated Testing:
Jalankan test suite yang sudah disediakan.

## Emergency Response

Jika terjadi security breach:
1. Matikan aplikasi sementara jika diperlukan
2. Reset semua user sessions
3. Review logs untuk investigasi
4. Update credentials yang compromised
5. Deploy patch jika diperlukan
