<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SignatureHelper
{
    // Proses dan simpan gambar tanda tangan base64
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

    // Hapus file tanda tangan
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
