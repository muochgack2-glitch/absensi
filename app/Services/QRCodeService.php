<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
     * Build token untuk barcode EAN-13 (format angka murni).
     * Kompatibel dengan scanner EP5000G yang hanya bisa baca EAN.
     *
     * Format: NIS (6 digit, zero-padded) + hash HMAC 6 digit numerik = 12 digit
     * Picqer akan tambah check digit → 13 digit EAN-13
     *
     * @param string $nis
     * @return string 12-digit numeric token
     */
    public function buildQRToken(string $nis): string
    {
        $secret  = config('app.key');
        $nisOnly = preg_replace('/[^0-9]/', '', $nis); // angka saja

        // 6-digit HMAC numerik dari SHA256
        $hmac    = hash_hmac('sha256', $nisOnly, $secret);
        $numHash = sprintf('%06d', hexdec(substr($hmac, 0, 6)) % 1000000);

        // NIS 6 digit (zero-padded) + hash 6 digit = 12 digit → EAN-13
        return str_pad($nisOnly, 6, '0', STR_PAD_LEFT) . $numHash;
    }

    /**
     * Verifikasi token EAN-13 dan kembalikan NIS jika valid.
     *
     * EP5000G output 13 digit (termasuk check digit EAN-13).
     * Webcam/scanner lain mungkin output 12 digit (tanpa check digit).
     *
     * @param string $token  angka dari scanner (12 atau 13 digit)
     * @return string|null   NIS on success, null on failure
     */
    public function verifyQRToken(string $token): ?string
    {
        // Strip karakter non-angka (spasi, newline dari HID scanner)
        $token = preg_replace('/[^0-9]/', '', $token);

        // EP5000G output 13 digit (include EAN check digit) → strip terakhir
        if (strlen($token) === 13) {
            $token = substr($token, 0, 12);
        }

        // Harus 12 digit angka murni
        if (strlen($token) !== 12) {
            return null;
        }

        $nisNum  = substr($token, 0, 6); // 6 digit NIS
        $hashRec = substr($token, 6, 6); // 6 digit hash

        // Hapus leading zeros untuk dapat NIS asli
        $nis = ltrim($nisNum, '0') ?: '0';

        // Hitung ulang hash
        $secret  = config('app.key');
        $hmac    = hash_hmac('sha256', $nis, $secret);
        $hashExp = sprintf('%06d', hexdec(substr($hmac, 0, 6)) % 1000000);

        if (!hash_equals($hashExp, $hashRec)) {
            return null; // token tidak valid
        }

        return $nis; // kembalikan NIS
    }

    public function generateQRCode(string $nis): string
    {
        // 12-digit numeric token untuk EAN-13
        $qrContent = $this->buildQRToken($nis);

        // Generate EAN-13 barcode — kompatibel dengan EP5000G
        if (class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
            $generator  = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeRaw = $generator->getBarcode(
                $qrContent,
                $generator::TYPE_EAN_13,
                5,   // bar width 5px
                120  // height 120px
            );

            // Picqer v3 pakai transparent background → fix dengan white background
            // Tanpa ini barcode tampak terbalik (putih di hitam) → tidak bisa discan
            $src   = imagecreatefromstring($barcodeRaw);
            $w     = imagesx($src);
            $h     = imagesy($src);
            $dst   = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $white);
            imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
            ob_start();
            imagepng($dst);
            $barcodeImage = ob_get_clean();
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            // Fallback GD jika picqer tidak ada
            $barcodeImage = $this->generateCode128GD($qrContent);
        }

        $path = "qrcodes/{$nis}.png";
        Storage::disk('public')->put($path, $barcodeImage);

        return $path;
    }

    /**
     * Generate Code128B barcode PNG menggunakan GD (tanpa library eksternal).
     * Tabel patterns lengkap 103 entri sesuai standar Code128.
     */
    private function generateCode128GD(string $text): string
    {
        // Code128B value table: ASCII 32-127 → Code128 value 0-95
        $code128B = [];
        for ($i = 32; $i <= 127; $i++) {
            $code128B[chr($i)] = $i - 32;
        }

        // Complete Code128 patterns — 103 data symbols (0-102) + start/stop
        $patterns = [
            '11011001100', '11001101100', '11001100110', '10010011000',
            '10010001100', '10001001100', '10011001000', '10011000100',
            '10001100100', '11001001000', '11001000100', '11000100100',
            '10110011100', '10011011100', '10011001110', '10111001100',
            '10011101100', '10011100110', '11001110010', '11001011100',
            '11001001110', '11011100100', '11001110100', '11101101110',
            '11101001100', '11100101100', '11100100110', '11101100100',
            '11100110100', '11100110010', '11011011000', '11011000110',
            '11000110110', '10100011000', '10001011000', '10001000110',
            '10110001000', '10001101000', '10001100010', '11010001000',
            '11000101000', '11000100010', '10110111000', '10110001110',
            '10001101110', '10111011000', '10111000110', '10001110110',
            '11101110110', '11010001110', '11000101110', '11011101000',
            '11011100010', '11011101110', '11101011000', '11101000110',
            '11100010110', '11101101000', '11101100010', '11100011010',
            '11101111010', '11001000010', '11110001010', '10100110000',
            '10100001100', '10010110000', '10010000110', '10000101100',
            '10000100110', '10110010000', '10110000100', '10011010000',
            '10011000010', '10000110100', '10000110010', '11000010010',
            '11001010000', '11110111010', '11000010100', '10001111010',
            '10100111100', '10010111100', '10010011110', '10111100100',
            '10011110100', '10011110010', '11110100100', '11110010100',
            '11110010010', '11011011110', '11011110110', '11110110110',
            '10101111000', '10100011110', '10001011110', '10111101000',
            '10111100010', '11110101000', '11110100010', '10111011110',
            '10111101110', '11101011110', '11110101110', '11010000100',
            '11010010000', // 104 = Start B (index 104 - tapi dipakai terpisah)
        ];

        // Build data values
        $checksum   = 104; // Start Code B
        $codeValues = [];
        foreach (str_split($text) as $char) {
            $val          = $code128B[$char] ?? 0;
            $codeValues[] = $val;
            $checksum    += $val * count($codeValues);
        }
        $checksum %= 103;

        // Build bit stream: StartB + data + checksum + stop
        $startB   = '11010010000';
        $stopPat  = '1100011101011';
        $bits     = $startB;
        foreach ($codeValues as $val) {
            $bits .= $patterns[$val] ?? $patterns[0];
        }
        $bits .= $patterns[$checksum] ?? $patterns[0];
        $bits .= $stopPat;

        // Render PNG dengan GD
        $barWidth = 3;
        $height   = 100;
        $quiet    = 20;
        $width    = strlen($bits) * $barWidth + $quiet * 2;

        $img   = imagecreatetruecolor($width, $height + 30);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0,   0,   0);
        imagefill($img, 0, 0, $white);

        $x = $quiet;
        foreach (str_split($bits) as $bit) {
            if ($bit === '1') {
                imagefilledrectangle($img, $x, 10, $x + $barWidth - 1, $height + 10, $black);
            }
            $x += $barWidth;
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png;
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
