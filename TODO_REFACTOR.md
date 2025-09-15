# Refaktor AgendaDetailController untuk Konsistensi Keamanan Tanda Tangan

## ✅ Perbaikan yang Akan Dilakukan

### 1. Update AgendaDetailController
- [ ] Import SignatureHelper di AgendaDetailController
- [ ] Update method store() untuk menggunakan SignatureHelper::processSignature()
- [ ] Update method update() untuk menggunakan SignatureHelper::processSignature() dan deleteSignature()
- [ ] Update method destroy() untuk menggunakan SignatureHelper::deleteSignature()
- [ ] Hapus import Storage yang tidak diperlukan

### 2. Update View Files
- [ ] Update resources/views/admin/agenda-detail/edit.blade.php untuk menggunakan protected route
- [ ] Update resources/views/admin/agenda-detail/show.blade.php untuk menggunakan protected route

### 3. Testing
- [ ] Test upload tanda tangan baru
- [ ] Test update tanda tangan (hapus yang lama, upload yang baru)
- [ ] Test delete peserta (hapus file tanda tangan)
- [ ] Test akses tanda tangan melalui admin panel

## 📋 Ringkasan Perubahan

**Masalah Saat Ini:** AgendaDetailController masih menggunakan disk public dan Storage facade secara langsung untuk file tanda tangan.

**Solusi:** Refaktor untuk menggunakan SignatureHelper dan disk private seperti ParticipantController.

**File yang Akan Diubah:**
- `app/Http/Controllers/AgendaDetailController.php`
- `resources/views/admin/agenda-detail/edit.blade.php`
- `resources/views/admin/agenda-detail/show.blade.php`
