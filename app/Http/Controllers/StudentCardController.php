<?php

namespace App\Http\Controllers;

use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;

class StudentCardController extends Controller
{
    /**
     * Generate QR Code PNG menggunakan BaconQrCode matrix + PHP GD
     * (Tidak butuh Imagick, hanya GD)
     *
     * @param string $content  Teks yang di-encode ke QR
     * @param int    $size     Ukuran output image dalam pixel
     * @return string|null     base64 data URI (data:image/png;base64,...) atau null jika gagal
     */
    private function generateQrPng(string $content, int $size = 200): ?string
    {
        try {
            $ecl    = ErrorCorrectionLevel::H();
            $qrCode = Encoder::encode($content, $ecl);
            $matrix = $qrCode->getMatrix();
            $cells  = $matrix->getWidth();

            $padding = 4;
            $scale   = (int) floor(($size - $padding * 2) / $cells);
            if ($scale < 1) {
                $scale = 1;
            }
            $imgSize = $cells * $scale + $padding * 2;

            $im    = imagecreatetruecolor($imgSize, $imgSize);
            $white = imagecolorallocate($im, 255, 255, 255);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefill($im, 0, 0, $white);

            for ($y = 0; $y < $cells; $y++) {
                for ($x = 0; $x < $cells; $x++) {
                    if ($matrix->get($x, $y) === 1) {
                        imagefilledrectangle(
                            $im,
                            $x * $scale + $padding,
                            $y * $scale + $padding,
                            ($x + 1) * $scale + $padding - 1,
                            ($y + 1) * $scale + $padding - 1,
                            $black
                        );
                    }
                }
            }

            ob_start();
            imagepng($im);
            $pngData = ob_get_clean();
            imagedestroy($im);

            return 'data:image/png;base64,' . base64_encode($pngData);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show print preview / options page
     */
    public function index(Request $request)
    {
        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('attendance.students.card-options', compact('classes'));
    }

    /**
     * Generate PDF kartu pelajar (bulk)
     */
    public function generate(Request $request)
    {
        // Naikkan memory limit untuk proses render banyak kartu
        ini_set('memory_limit', '512M');

        $request->validate([
            'kelas_id'   => 'nullable|exists:attendance_classes,id',
            'student_ids'=> 'nullable|string',
            'layout'     => 'required|in:2x5,2x4,2x3',
        ]);

        // Get students based on selection
        $query = AttendanceStudent::with('kelas')->where('is_active', true);

        if ($request->filled('student_ids')) {
            $ids = explode(',', $request->student_ids);
            $query->whereIn('id', $ids);
        } elseif ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $students = $query->orderBy('nama', 'asc')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa yang ditemukan.');
        }

        // Get school settings
        $settings      = AttendanceSetting::getGrouped();
        $schoolName    = $settings['general']['school_name']    ?? 'SMK PGRI BLORA';
        $schoolAddress = $settings['general']['school_address'] ?? '';

        // Logo
        $logoPath   = $settings['general']['school_logo'] ?? null;
        $logoBase64 = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoContent = Storage::disk('public')->get($logoPath);
            $logoMime    = Storage::disk('public')->mimeType($logoPath);
            $logoBase64  = 'data:' . $logoMime . ';base64,' . base64_encode($logoContent);
        }

        // Generate data per siswa
        $studentData = [];
        foreach ($students as $student) {
            // QR Code sebagai PNG via GD (tidak butuh Imagick)
            $qrBase64 = $this->generateQrPng($student->nis, 200);

            // Foto profil
            $fotoBase64 = null;
            if ($student->foto_profil && Storage::disk('public')->exists($student->foto_profil)) {
                $fotoContent = Storage::disk('public')->get($student->foto_profil);
                $fotoMime    = Storage::disk('public')->mimeType($student->foto_profil);
                $fotoBase64  = 'data:' . $fotoMime . ';base64,' . base64_encode($fotoContent);
            }

            $studentData[] = [
                'student'    => $student,
                'qr_base64'  => $qrBase64,
                'foto_base64'=> $fotoBase64,
            ];
        }

        // Layout config
        $layout       = $request->layout;
        $layoutConfig = [
            '2x5' => ['cols' => 2, 'rows' => 5, 'per_page' => 10],
            '2x4' => ['cols' => 2, 'rows' => 4, 'per_page' => 8],
            '2x3' => ['cols' => 2, 'rows' => 3, 'per_page' => 6],
        ];
        $config = $layoutConfig[$layout];

        // Chunk per halaman
        $pages       = array_chunk($studentData, $config['per_page']);
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

        $pdf = Pdf::loadView('attendance.students.card-print', compact(
            'pages', 'config', 'layout',
            'schoolName', 'schoolAddress', 'logoBase64',
            'tahunAjaran'
        ))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        // F4 paper size: 215mm x 330mm
        $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');

        return $pdf->stream('kartu-pelajar.pdf');
    }

    /**
     * Cetak kartu pelajar untuk 1 siswa
     *
     * GET /attendance/students/{student}/print-qr
     */
    public function printSingle(AttendanceStudent $student)
    {
        $student->load('kelas');

        // Settings
        $settings      = AttendanceSetting::getGrouped();
        $schoolName    = $settings['general']['school_name']    ?? 'SMK PGRI BLORA';
        $schoolAddress = $settings['general']['school_address'] ?? '';

        // Logo
        $logoPath   = $settings['general']['school_logo'] ?? null;
        $logoBase64 = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoContent = Storage::disk('public')->get($logoPath);
            $logoMime    = Storage::disk('public')->mimeType($logoPath);
            $logoBase64  = 'data:' . $logoMime . ';base64,' . base64_encode($logoContent);
        }

        // QR Code sebagai PNG via GD
        $qrBase64 = $this->generateQrPng($student->nis, 250);

        // Foto
        $fotoBase64 = null;
        if ($student->foto_profil && Storage::disk('public')->exists($student->foto_profil)) {
            $fotoContent = Storage::disk('public')->get($student->foto_profil);
            $fotoMime    = Storage::disk('public')->mimeType($student->foto_profil);
            $fotoBase64  = 'data:' . $fotoMime . ';base64,' . base64_encode($fotoContent);
        }

        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

        $pdf = Pdf::loadView('attendance.students.card-single', compact(
            'student', 'qrBase64', 'fotoBase64',
            'logoBase64', 'schoolName', 'schoolAddress', 'tahunAjaran'
        ))->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('kartu-' . $student->nis . '.pdf');
    }
}
