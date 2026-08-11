<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AttendanceStudent;
use Illuminate\Support\Collection;

class QRCardPdfService
{
    /**
     * Generate PDF with QR code cards in grid layout.
     *
     * @param Collection|array $students Collection or array of student objects
     * @param string $layout Grid layout: '3x3' (default), '4x4', or '6x6'
     * @param bool $includeClass Whether to include class name in card
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePDF($students, string $layout = '3x3', bool $includeClass = false)
    {
        if (!is_array($students) && !($students instanceof Collection)) {
            $students = [$students];
        }

        // Convert to array if Collection
        if ($students instanceof Collection) {
            $students = $students->toArray();
        }

        // Get layout dimensions
        $dimensions = $this->getLayoutDimensions($layout);
        
        // Group students into pages based on layout
        $pages = array_chunk($students, $dimensions['cards_per_page']);

        // Get school name from settings
        $schoolName = config('app.school_name', 'SMK SPMB');

        // Prepare data for view
        $data = [
            'pages' => $pages,
            'layout' => $layout,
            'dimensions' => $dimensions,
            'includeClass' => $includeClass,
            'schoolName' => $schoolName,
        ];

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdfs.qr-cards', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('dpi', 300)
            ->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Get layout dimensions based on selected layout.
     *
     * @param string $layout
     * @return array Layout dimensions with card size, columns, rows, and cards per page
     */
    public function getLayoutDimensions(string $layout): array
    {
        $layouts = [
            '3x3' => [
                'cols' => 3,
                'rows' => 3,
                'cards_per_page' => 9,
                'card_width_mm' => 50,    // 5cm
                'card_height_mm' => 60,   // 6cm
                'qr_size_mm' => 47,       // QR code size
                'grid_gap_mm' => 4,       // Spacing between cards
            ],
            '4x4' => [
                'cols' => 4,
                'rows' => 4,
                'cards_per_page' => 16,
                'card_width_mm' => 40,    // Smaller for 4x4
                'card_height_mm' => 50,
                'qr_size_mm' => 35,
                'grid_gap_mm' => 3,
            ],
            '6x6' => [
                'cols' => 6,
                'rows' => 6,
                'cards_per_page' => 36,
                'card_width_mm' => 28,    // Even smaller for 6x6
                'card_height_mm' => 35,
                'qr_size_mm' => 24,
                'grid_gap_mm' => 2,
            ],
        ];

        return $layouts[$layout] ?? $layouts['3x3'];
    }

    /**
     * Generate QR code content as data URL (SVG or PNG).
     *
     * @param string $nis Student NIS
     * @return string Base64 encoded data URL
     */
    public function getQRCodeDataUrl(string $nis): string
    {
        $qrCodeService = app(QRCodeService::class);
        
        try {
            // Get QR code as SVG
            $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($nis);
            
            // Convert to data URL
            $dataUrl = 'data:image/svg+xml;base64,' . base64_encode($qrImage);
            return $dataUrl;
        } catch (\Exception $e) {
            // Return placeholder if QR generation fails
            return '';
        }
    }
}
