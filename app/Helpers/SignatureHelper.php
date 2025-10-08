<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SignatureHelper
{
    // Proses dan simpan gambar tanda tangan base64 dengan optimasi
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

                // Optimasi gambar menggunakan GD library
                $optimizedImageData = self::optimizeSignatureImage($imageData);

                // Buat nama file yang unik - selalu gunakan format PNG
                $filename = 'signature_' . time() . '_' . uniqid() . '.png';
                $path = 'tandatangan/' . $filename;

                // Simpan file yang sudah dioptimasi ke disk private
                Storage::disk('local')->put($path, $optimizedImageData);

                Log::info('Tanda tangan berhasil disimpan dan dioptimasi: ' . $path);

                return $path;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gagal memproses tanda tangan: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan tanda tangan digital.');
        }
    }

    // Optimasi gambar tanda tangan dengan resize dan kompresi
    private static function optimizeSignatureImage($imageData)
    {
        try {
            // Buat image resource dari string
            $image = imagecreatefromstring($imageData);
            if (!$image) {
                throw new \Exception('Gagal membuat image resource dari data.');
            }

            // Dapatkan dimensi asli
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Target dimensi maksimal untuk tanda tangan
            $maxWidth = 400;
            $maxHeight = 200;

            // Hitung rasio untuk resize
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

            // Jika gambar sudah lebih kecil dari target, tidak perlu resize
            if ($ratio >= 1) {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            } else {
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
            }

            // Buat image baru dengan dimensi yang dioptimasi
            $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Set transparent background untuk PNG
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
            imagefill($optimizedImage, 0, 0, $transparent);

            // Resize gambar
            imagecopyresampled($optimizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Simpan sebagai PNG dengan kompresi
            ob_start();
            imagepng($optimizedImage, null, 8); // Quality 8 untuk balance antara ukuran dan kualitas
            $optimizedData = ob_get_clean();

            // Bersihkan memory
            imagedestroy($image);
            imagedestroy($optimizedImage);

            Log::info('Gambar tanda tangan dioptimasi: ' . strlen($imageData) . ' bytes -> ' . strlen($optimizedData) . ' bytes');

            return $optimizedData;

        } catch (\Exception $e) {
            Log::error('Gagal mengoptimasi gambar tanda tangan: ' . $e->getMessage());
            // Jika gagal optimasi, kembalikan data asli
            return $imageData;
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
