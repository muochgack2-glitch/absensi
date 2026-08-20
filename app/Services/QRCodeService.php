<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    /**
     * Generate QR Code for a student and save to storage.
     *
     * @param string $nis Student NIS (Nomor Induk Siswa)
     * @return string Path to saved QR code file
     */
    /**
     * Build a signed QR token for the given NIS.
     *
     * Format: "<NIS>:<HMAC-SHA256(NIS, APP_KEY)>"
     * The HMAC prevents forging a valid token without knowing APP_KEY.
     *
     * @param string $nis
     * @return string signed token
     */
    public function buildQRToken(string $nis): string
    {
        $secret = config('app.key');
        // 8-char uppercase HMAC → QR Version 1 Alfanumerik → scan SUPER CEPAT
        $sig = strtoupper(substr(hash_hmac('sha256', $nis, $secret), 0, 8));
        return strtoupper($nis) . ':' . $sig;
    }

    /**
     * Verify a QR token and return the NIS if valid.
     *
     * Returns null if the token is malformed or the signature does not match.
     *
     * @param string $token  raw string scanned from QR
     * @return string|null   NIS on success, null on failure
     */
    public function verifyQRToken(string $token): ?string
    {
        $parts = explode(':', $token, 2);
        if (count($parts) !== 2) {
            return null; // malformed
        }

        [$nis, $receivedSig] = $parts;
        $nis = strtoupper($nis);

        $secret      = config('app.key');
        // Hitung 8-char uppercase HMAC sesuai format baru
        $expectedSig = strtoupper(substr(hash_hmac('sha256', strtolower($nis), $secret), 0, 8));

        // Use hash_equals to prevent timing attacks
        if (!hash_equals($expectedSig, strtoupper($receivedSig))) {
            return null; // invalid signature
        }

        return strtolower($nis); // kembalikan NIS dalam lowercase
    }

    public function generateQRCode(string $nis): string
    {
        // Generate signed token — sama seperti sebelumnya
        $qrContent = $this->buildQRToken($nis);
        
        // Generate Code128 Barcode (1D) — mudah dibaca printer biasa & EP5000G
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = $generator->getBarcode(
            $qrContent,
            $generator::TYPE_CODE_128,
            3,    // lebar setiap bar (px)
            100   // tinggi barcode (px)
        );
        
        // Simpan sebagai PNG
        $path = "qrcodes/{$nis}.png";
        Storage::disk('public')->put($path, $barcodeImage);
        
        return $path;
    }

    /**
     * Regenerate QR Code for a student (delete old and create new).
     *
     * @param string $nis Student NIS
     * @return string Path to new QR code file
     */
    public function regenerateQRCode(string $nis): string
    {
        // Hapus file lama (SVG dan PNG)
        foreach (['svg', 'png'] as $ext) {
            $oldPath = "qrcodes/{$nis}.{$ext}";
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        // Generate new QR Code
        return $this->generateQRCode($nis);
    }

    /**
     * Get public URL for QR Code.
     *
     * @param string $nis Student NIS
     * @return string|null Public URL or null if not found
     */
    public function getQRCodeUrl(string $nis): ?string
    {
        // Cek PNG dulu, fallback ke SVG
        foreach (['png', 'svg'] as $ext) {
            $path = "qrcodes/{$nis}.{$ext}";
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        }
        return null;
    }

    /**
     * Generate QR Codes for multiple students in batch.
     *
     * @param array $students Array of student objects with 'nis' property
     * @return array Array of results with 'nis', 'path', 'success', 'error'
     */
    public function generateBatchQRCodes(array $students): array
    {
        $results = [];
        
        foreach ($students as $student) {
            try {
                $path = $this->generateQRCode($student->nis);
                
                $results[] = [
                    'nis' => $student->nis,
                    'nama' => $student->nama ?? null,
                    'path' => $path,
                    'success' => true,
                    'error' => null,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'nis' => $student->nis,
                    'nama' => $student->nama ?? null,
                    'path' => null,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }

    /**
     * Delete QR Code file for a student.
     *
     * @param string $nis Student NIS
     * @return bool Success status
     */
    public function deleteQRCode(string $nis): bool
    {
        $path = "qrcodes/{$nis}.svg";
        
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return true; // Already deleted
    }

    /**
     * Check if QR Code exists for a student.
     *
     * @param string $nis Student NIS
     * @return bool
     */
    public function qrCodeExists(string $nis): bool
    {
        foreach (['png', 'svg'] as $ext) {
            if (Storage::disk('public')->exists("qrcodes/{$nis}.{$ext}")) {
                return true;
            }
        }
        return false;
    }

    /**
     * Convert a stored QR code file (path relative to storage/app/public)
     * into a base64 string along with the CORRECT mime type, so it can be
     * embedded directly in an <img src="data:{mime};base64,{data}"> tag.
     *
     * QR codes are always saved as SVG. If the Imagick PHP extension is
     * available, we convert the SVG to PNG (better compatibility with some
     * PDF renderers). If Imagick is NOT available, we fall back to using
     * the raw SVG data — but critically, we report the mime type as
     * "image/svg+xml" in that case, not "image/png". Previously this mime
     * type was hardcoded to image/png everywhere the SVG was embedded,
     * which produced a broken image whenever Imagick was missing (the
     * browser/PDF renderer received SVG/XML data labeled as PNG and failed
     * to decode it).
     *
     * @param string $relativePath Path relative to storage/app/public (e.g. "qrcodes/12345.svg")
     * @return array{base64: ?string, mime: ?string} 'base64' and 'mime' are null if the file is missing/unreadable
     */
    public function getQRCodeAsBase64(string $relativePath): array
    {
        $fullPath = storage_path('app/public/' . $relativePath);

        if (!$relativePath || !file_exists($fullPath)) {
            return ['base64' => null, 'mime' => null];
        }

        $isSvg = str_ends_with(strtolower($fullPath), '.svg');

        if ($isSvg) {
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->setBackgroundColor(new \ImagickPixel('white'));
                    $imagick->readImage($fullPath);
                    $imagick->setImageFormat('png');
                    return [
                        'base64' => base64_encode($imagick->getImageBlob()),
                        'mime' => 'image/png',
                    ];
                } catch (\Exception $e) {
                    // Fall through to raw SVG fallback below.
                }
            }

            // Imagick unavailable (or conversion failed): use raw SVG data,
            // and report the mime type accurately as image/svg+xml.
            $svg = file_get_contents($fullPath);
            if ($svg === false) {
                return ['base64' => null, 'mime' => null];
            }

            return [
                'base64' => base64_encode($svg),
                'mime' => 'image/svg+xml',
            ];
        }

        // Non-SVG file (e.g. already PNG/JPEG) — detect mime from content.
        $data = file_get_contents($fullPath);
        if ($data === false) {
            return ['base64' => null, 'mime' => null];
        }

        $mime = function_exists('mime_content_type')
            ? (mime_content_type($fullPath) ?: 'image/png')
            : 'image/png';

        return [
            'base64' => base64_encode($data),
            'mime' => $mime,
        ];
    }
}
