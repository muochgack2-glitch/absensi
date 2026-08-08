<?php

namespace App\Exports;

use App\Models\AttendanceStudent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $classId;
    protected $status;

    public function __construct($classId = null, $status = null)
    {
        $this->classId = $classId;
        $this->status = $status;
    }

    public function collection()
    {
        $query = AttendanceStudent::with('kelas')->orderBy('nama', 'asc');

        if ($this->classId) {
            $query->where('kelas_id', $this->classId);
        }

        if ($this->status !== null) {
            $query->where('is_active', $this->status === 'active');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'No HP Orang Tua',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function map($student): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $student->nis,
            $student->nama,
            $student->kelas->nama_kelas ?? '-',
            $student->kelas->jurusan ?? '-',
            $student->no_hp_ortu ?? '-',
            $student->is_active ? 'Aktif' : 'Tidak Aktif',
            $student->created_at?->format('d/m/Y') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
