<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class QRCardPdfService
{
    private QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate PDF dengan kartu QR dalam grid layout
     * 
     * @param array $students Array of student objects dengan properties: nis, nama, kelas.nama_kelas
     * @param string $layout '3x3' (9 kartu per halaman)
     * @param bool $includeClass Tampilkan nama kelas di kartu
     * @param string $schoolName Nama sekolah (untuk ditampilkan di kartu)
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePDF(
        array $students,
        string $layout = '3x3',
        bool $includeClass = true,
        string $schoolName = 'SMK SPMB'
    ) {
        // Validate layout
        if (!in_array($layout, ['3x3'])) {
            $layout = '3x3';
        }

        // Get layout dimensions
        $dimensions = $this->getLayoutDimensions($layout);

        // Chunk students berdasarkan layout (9 untuk 3x3)
        $cardPerPage = $dimensions['cards_per_page'];
        $chunks = array_chunk($students, $cardPerPage);

        // Prepare data untuk view
        $pages = [];
        foreach ($chunks as $chunk) {
            // Ensure each page has exactly cardPerPage items (fill with empty if needed)
            while (count($chunk) < $cardPerPage) {
                $chunk[] = null;
            }
            $pages[] = $chunk;
        }

        // Generate PDF using Blade template
        $pdf = Pdf::loadView('pdfs.qr-cards', [
            'pages' => $pages,
            'layout' => $layout,
            'dimensions' => $dimensions,
            'includeClass' => $includeClass,
            'schoolName' => $schoolName,
        ]);

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Get layout dimensions berdasarkan layout type
     * 
     * @param string $layout
     * @return array
     */
    private function getLayoutDimensions(string $layout): array
    {
        return match($layout) {
            '3x3' => [
                'cols' => 3,
                'rows' => 3,
                'cards_per_page' => 9,
                'card_width_mm' => 50,      // 5cm
                'card_height_mm' => 60,     // 6cm
                'card_width_px' => 189,     // 50mm in pixels (at 96dpi)
                'card_height_px' => 227,    // 60mm in pixels (at 96dpi)
                'gap_mm' => 4,
                'margin_mm' => 10,
            ],
            default => [
                'cols' => 3,
                'rows' => 3,
                'cards_per_page' => 9,
                'card_width_mm' => 50,
                'card_height_mm' => 60,
                'card_width_px' => 189,
                'card_height_px' => 227,
                'gap_mm' => 4,
                'margin_mm' => 10,
            ],
        };
    }
}
