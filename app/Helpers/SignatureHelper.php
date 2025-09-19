<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Helper class untuk mengelola tanda tangan digital
 *
 * Menangani proses penyimpanan, validasi, dan penghapusan
 * file tanda tangan digital dalam format base64.
 *
 * @package App\Helpers
 */
class SignatureHelper
{
    /**
     * Proses dan simpan gambar tanda tangan base64
     *
     * Mengkonversi data base64 menjadi file PNG dan menyimpannya
     * ke storage private dengan validasi ukuran dan format.
     *
     * @param string $signatureData Data tanda tangan dalam format base64
     * @return string|null Path file tanda tangan yang disimpan atau null jika gagal
     * @throws \Exception Jika terjadi kesalahan dalam pemrosesan
     */
    public static function processSignature($signatureData)
    {
        try {
            // Ekstrak data base64
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData, $matches)) {
                $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
                $imageData = base64_decode($base64Data);

                // Cek ukuran file maksimal 2MB
                if (strlen($imageData) > 2097152) { // 2MB in bytes
                    throw new \Exception('Ukuran file tanda tangan melebihi batas maksimal 2MB.');
                }

                // Buat nama file yang unik - selalu gunakan format PNG
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                $path = 'tandatangan/' . $filename;

                // Simpan file ke disk private
                Storage::disk('local')->put($path, $imageData);

                Log::info('Tanda tangan berhasil disimpan: ' . $path);

                return $path;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gagal memproses tanda tangan: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan tanda tangan digital.');
        }
    }

    /**
     * Hapus file tanda tangan dari storage
     *
     * Menghapus file tanda tangan dari disk storage private
     * dan mencatat aktivitas penghapusan ke log.
     *
     * @param string $path Path file tanda tangan yang akan dihapus
     * @return bool True jika berhasil dihapus, false jika gagal
     */
    public static function deleteSignature($path)
    {
        try {
            if ($path && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                Log::info('Tanda tangan dihapus: ' . $path);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Gagal menghapus tanda tangan: ' . $e->getMessage());
            return false;
        }
    }
}
