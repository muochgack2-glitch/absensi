<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data ?: $this->getDefaultData();
    }

    /**
     * Return array of data (contoh baris) untuk template.
     */
    public function array(): array
    {
        return [
            ['24001', 'Contoh Siswa 1', '1', '628123456789', ''],
            ['24002', 'Contoh Siswa 2', '1', '628123456790', '628987654321'],
            ['24003', 'Contoh Siswa 3', '2', '628123456791', ''],
        ];
    }

    /**
     * Heading kolom.
     */
    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Kelas ID',
            'No HP Ortu',
            'No HP Wali / Alternatif',
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Header row A1:E1
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data rows A2:E4
        $sheet->getStyle('A2:E4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Kolom E (no_hp_ortu2) diberi warna latar kuning muda sebagai penanda "opsional"
        $sheet->getStyle('E1:E4')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FEF9C3'], // yellow-100
            ],
        ]);
        // Tapi header tetap biru
        $sheet->getStyle('E1')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7C3AED'], // purple-700
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    /**
     * Lebar kolom.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIS
            'B' => 30, // Nama
            'C' => 12, // Kelas ID
            'D' => 22, // No HP Ortu
            'E' => 28, // No HP Wali / Alternatif
        ];
    }

    /**
     * Default data (tidak dipakai jika array() sudah di-override).
     */
    private function getDefaultData(): array
    {
        return [
            ['24001', 'Contoh Siswa 1', '1', '628123456789', ''],
            ['24002', 'Contoh Siswa 2', '1', '628123456790', '628987654321'],
        ];
    }
}
