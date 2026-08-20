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
        // Generate signed token
        $qrContent = $this->buildQRToken($nis);

        // Coba pakai picqer jika tersedia, fallback ke GD built-in
        if (class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
            $generator    = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeImage = $generator->getBarcode(
                $qrContent,
                $generator::TYPE_CODE_128,
                3,   // bar width
                100  // height
            );
        } else {
            // Fallback: generate barcode menggunakan GD (built-in PHP)
            $barcodeImage = $this->generateCode128GD($qrContent);
        }

        $path = "qrcodes/{$nis}.png";
        Storage::disk('public')->put($path, $barcodeImage);

        return $path;
    }

    /**
     * Generate Code128 barcode PNG menggunakan GD (tanpa library eksternal).
     */
    private function generateCode128GD(string $text): string
    {
        // Code128 B encoding table
        $code128B = [
            ' '=>0,  '!'=>1,  '"'=>2,  '#'=>3,  '$'=>4,  '%'=>5,  '&'=>6,  "'".'=>7,
            '('=>8,  ')'=>9,  '*'=>10, '+'=>11, ','=>12, '-'=>13, '.'=>14, '/'=>15,
            '0'=>16, '1'=>17, '2'=>18, '3'=>19, '4'=>20, '5'=>21, '6'=>22, '7'=>23,
            '8'=>24, '9'=>25, ':'=>26, ';'=>27, '<'=>28, '='=>29, '>'=>30, '?'=>31,
            '@'=>32, 'A'=>33, 'B'=>34, 'C'=>35, 'D'=>36, 'E'=>37, 'F'=>38, 'G'=>39,
            'H'=>40, 'I'=>41, 'J'=>42, 'K'=>43, 'L'=>44, 'M'=>45, 'N'=>46, 'O'=>47,
            'P'=>48, 'Q'=>49, 'R'=>50, 'S'=>51, 'T'=>52, 'U'=>53, 'V'=>54, 'W'=>55,
            'X'=>56, 'Y'=>57, 'Z'=>58, '['=>59, '\\'=>60, ']'=>61, '^'=>62, '_'=>63,
        ];

        // Pola bar tiap simbol (11 bit: 1=bar, 0=space)
        $patterns = [
            '11011001100','11001101100','11001100110','10010011000','10010001100',
            '10001001100','10011001000','10011000100','10001100100','11001001000',
            '11001000100','11000100100','10110011100','10011011100','10011001110',
            '10111001100','10011101100','10011100110','11001110010','11001011100',
            '11001001110','11011100100','11001110100','11101101110','11101001100',
            '11100101100','11100100110','11101100100','11100110100','11100110010',
            '11011011000','11011000110','11000110110','10100011000','10001011000',
            '10001000110','10110001000','10001101000','10001100010','11010001000',
            '11000101000','11000100010','10110111000','10110001110','10001101110',
            '10111011000','10111000110','10001110110','11101110110','11010001110',
            '11000101110','11011101000','11011100010','11011101110','11101011000',
            '11101000110','11100010110','11101101000','11101100010','11100011010',
            '11101111010','11001000010','11110001010','10100110000','10100001100',
        ];

        // Start Code B = index 104, stop = 106
        $startPattern  = '11010010000';
        $stopPattern   = '1100011101011';
        $checksum      = 104;
        $codeValues    = [];

        foreach (str_split($text) as $char) {
            $val = isset($code128B[$char]) ? $code128B[$char] : 0;
            $codeValues[] = $val;
            $checksum    += $val * (count($codeValues));
        }
        $checksum %= 103;

        // Build bit stream
        $bits = $startPattern;
        foreach ($codeValues as $val) {
            $bits .= $patterns[$val] ?? $patterns[0];
        }
        $bits .= $patterns[$checksum];
        $bits .= $stopPattern;

        // Draw PNG dengan GD
        $barWidth  = 3;
        $height    = 100;
        $quiet     = 20; // quiet zone kiri & kanan
        $width     = strlen($bits) * $barWidth + $quiet * 2;

        $img = imagecreatetruecolor($width, $height + 20);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        $x = $quiet;
        foreach (str_split($bits) as $bit) {
            if ($bit === '1') {
                imagefilledrectangle($img, $x, 10, $x + $barWidth - 1, $height + 10, $black);
            }
            $x += $barWidth;
        }

        // Output ke string
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
