# TODO: Code Improvements Based on Review

## Overview
Berdasarkan review kode aplikasi Laravel, berikut adalah daftar perbaikan yang perlu dilakukan untuk meningkatkan keamanan, performa, maintainability, dan UX.

## Security Enhancements
- [x] Tambahkan fitur "Remember Me" pada AuthController untuk login
- [x] Perkuat validasi input pada signature processing (ukuran file, tipe file, dimensi, base64 validation)
- [x] Perkuat validasi pada pendaftaran publik untuk mencegah spam (honeypot, reCAPTCHA, timing validation)
- [ ] Implementasi role-based access control (RBAC) yang lebih granular di AdminMiddleware
- [ ] Tambahkan audit logging untuk operasi CRUD penting
- [ ] Review dan perbaiki permission pada file signature serving

## Performance Optimization
- [x] Implementasi caching pada query sering diakses (daftar agenda, master dinas)
- [x] Optimasi eager loading untuk menghindari N+1 query
- [x] Review dan optimasi query di AdminController dashboard
- [ ] Batasi ukuran upload file signature (max 2MB sudah ada, pastikan enforced)
- [ ] Optimasi export PDF dengan queue untuk file besar
- [ ] Tambahkan indexing database jika diperlukan berdasarkan query analysis

## Code Quality & Maintainability
- [x] Refactor logic export PDF di AdminController ke service class
- [x] Pindahkan signature processing logic ke service class
- [ ] Buat service class untuk business logic kompleks
- [ ] Tambahkan unit tests untuk controller methods
- [ ] Tambahkan feature tests untuk critical flows
- [ ] Perbaiki dokumentasi PHPDoc pada method kompleks
- [ ] Review dan hapus duplikasi kode

## User Experience Improvements
- [x] Upgrade sidebar dan footer menggunakan theme login (gradient background)
- [x] Tambahkan validasi client-side pada form pendaftaran
- [ ] Tambahkan validasi client-side pada form admin
- [ ] Perbaiki UI/UX pada halaman pendaftaran publik
- [ ] Perbaiki tampilan manajemen peserta
- [ ] Tambahkan loading indicators pada AJAX operations
- [ ] Perbaiki notifikasi dan feedback messages

## Monitoring & Backup
- [ ] Implementasi error monitoring (Sentry/Laravel Telescope)
- [ ] Tambahkan backup otomatis untuk database
- [ ] Tambahkan backup otomatis untuk file signatures
- [ ] Setup log rotation dan monitoring

## Files to Edit
- app/Http/Controllers/AuthController.php
- app/Http/Controllers/AdminController.php
- app/Http/Controllers/ParticipantController.php
- app/Http/Controllers/PublicAgendaController.php
- app/Http/Middleware/AdminMiddleware.php
- app/Helpers/SignatureHelper.php
- app/Models/ (untuk caching dan relationships)
- resources/js/ (validasi client-side)
- tests/ (unit dan feature tests)
- config/ (untuk caching dan monitoring)

## Implementation Order (Prioritized)
1. Security fixes (remember me, input validation)
2. Performance optimizations (caching, query optimization)
3. Code refactoring (service classes, tests)
4. UX improvements (client-side validation, UI fixes)
5. Monitoring and backup setup

## Notes
- Implementasi dilakukan secara bertahap untuk menghindari breaking changes
- Setiap perubahan di-test secara menyeluruh
- Backup database sebelum melakukan perubahan signifikan
- Update dokumentasi setelah perubahan
