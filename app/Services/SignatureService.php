<?php

namespace App\Services;

use App\Helpers\SignatureHelper;
use Illuminate\Support\Facades\Log;

class SignatureService
{
    /**
     * Validate and process signature data
     *
     * @param string|null $signatureData Base64 encoded image data
     * @param string|null $oldSignature Path to old signature file (for cleanup)
     * @return string|null Processed signature path or null
     * @throws \Exception
     */
    public function validateAndProcessSignature(?string $signatureData, ?string $oldSignature = null): ?string
    {
        if (!$signatureData || strpos($signatureData, 'data:image/') !== 0) {
            return null;
        }

        $this->validateSignatureFormat($signatureData);
        $this->validateSignatureSize($signatureData);
        $this->validateBase64Data($signatureData);
        $this->validateImageDimensions($signatureData);

        // Clean up old signature if exists
        if ($oldSignature && !str_contains($oldSignature, 'data:image/')) {
            SignatureHelper::deleteSignature($oldSignature);
        }

        // Process and save signature
        try {
            return SignatureHelper::processSignature($signatureData);
        } catch (\Exception $e) {
            Log::error('Failed to process signature: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan tanda tangan: ' . $e->getMessage());
        }
    }

    /**
     * Validate signature data format
     *
     * @param string $signatureData
     * @throws \Exception
     */
    private function validateSignatureFormat(string $signatureData): void
    {
        if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $signatureData)) {
            throw new \Exception('Tipe file tanda tangan harus PNG atau JPG.');
        }
    }

    /**
     * Validate signature file size
     *
     * @param string $signatureData
     * @throws \Exception
     */
    private function validateSignatureSize(string $signatureData): void
    {
        $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
        $fileSize = (strlen($base64Data) * 3 / 4); // Approximate decoded size

        if ($fileSize > 2097152) { // 2MB in bytes
            throw new \Exception('Ukuran file tanda tangan maksimal 2MB.');
        }
    }

    /**
     * Validate base64 data integrity
     *
     * @param string $signatureData
     * @throws \Exception
     */
    private function validateBase64Data(string $signatureData): void
    {
        $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);

        // Validate minimum length
        if (strlen($base64Data) < 100) {
            throw new \Exception('Data tanda tangan tidak valid.');
        }

        // Validate base64 format
        if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $base64Data)) {
            throw new \Exception('Format data tanda tangan tidak valid.');
        }

        // Validate decodability
        $decodedData = base64_decode($base64Data, true);
        if ($decodedData === false) {
            throw new \Exception('Data tanda tangan tidak dapat diproses.');
        }
    }

    /**
     * Validate image dimensions
     *
     * @param string $signatureData
     * @throws \Exception
     */
    private function validateImageDimensions(string $signatureData): void
    {
        $base64Data = substr($signatureData, strpos($signatureData, ',') + 1);
        $decodedData = base64_decode($base64Data);

        $imageInfo = getimagesizefromstring($decodedData);
        if (!$imageInfo) {
            throw new \Exception('Data tanda tangan bukan gambar valid.');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Minimum dimensions
        if ($width < 50 || $height < 20) {
            throw new \Exception('Tanda tangan terlalu kecil, minimal 50x20 pixels.');
        }

        // Maximum dimensions
        if ($width > 2000 || $height > 2000) {
            throw new \Exception('Tanda tangan terlalu besar, maksimal 2000x2000 pixels.');
        }
    }

    /**
     * Delete signature file
     *
     * @param string|null $signaturePath
     * @return bool
     */
    public function deleteSignature(?string $signaturePath): bool
    {
        if (!$signaturePath || str_contains($signaturePath, 'data:image/')) {
            return false;
        }

        return SignatureHelper::deleteSignature($signaturePath);
    }
}
