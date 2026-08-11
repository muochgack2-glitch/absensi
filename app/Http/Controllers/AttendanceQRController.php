<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use App\Services\QRCardPdfService;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceQRController extends Controller
{
    public function __construct(
        private QRCodeService $qrCodeService,
        private QRCardPdfService $qrCardPdfService
    ) {}

    /**
     * Show QR Code for a student.
     * 
     * GET /attendance/qr/{nis}
     * 
     * @param string $nis
     * @return \Illuminate\View\View
     */
    public function show(string $nis)
    {
        $student = AttendanceStudent::where('nis', $nis)
            ->with('kelas')
            ->firstOrFail();

        // Generate QR if not exists
        if (!$student->qr_code_path || !Storage::disk('public')->exists($student->qr_code_path)) {
            $path = $this->qrCodeService->generateQRCode($student->nis);
            $student->update(['qr_code_path' => $path]);
        }

        return view('attendance.qr.show', compact('student'));
    }

    /**
     * Download QR Code as file.
     * 
     * GET /attendance/qr/{nis}/download
     * 
     * @param string $nis
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $nis)
    {
        $student = AttendanceStudent::where('nis', $nis)->firstOrFail();

        // Generate QR if not exists
        if (!$student->qr_code_path || !Storage::disk('public')->exists($student->qr_code_path)) {
            $path = $this->qrCodeService->generateQRCode($student->nis);
            $student->update(['qr_code_path' => $path]);
        }

        $filename = "QR_{$student->nis}_{$student->nama}.svg";

        return Storage::disk('public')->download($student->qr_code_path, $filename);
    }

    /**
     * Regenerate QR Code for a student (admin only).
     * 
     * POST /attendance/qr/{nis}/regenerate
     * 
     * @param string $nis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerate(string $nis)
    {
        $student = AttendanceStudent::where('nis', $nis)->firstOrFail();

        // Regenerate QR Code
        $path = $this->qrCodeService->regenerateQRCode($student->nis);
        $student->update(['qr_code_path' => $path]);

        return redirect()->back()->with('success', 'QR Code berhasil di-generate ulang');
    }

    /**
     * Generate QR Code untuk SEMUA siswa aktif yang belum punya QR (bulk).
     *
     * POST /attendance/qr/bulk-generate
     */
    public function bulkGenerate(Request $request)
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '300');

        $onlyMissing = $request->input('only_missing', true);

        $query = AttendanceStudent::where('is_active', true);

        if ($onlyMissing) {
            // Hanya siswa yang belum punya QR (null di DB)
            $query->whereNull('qr_code_path');
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('info', 'Semua siswa aktif sudah memiliki QR Code.');
        }

        $results  = $this->qrCodeService->generateBatchQRCodes($students->all());
        $success  = collect($results)->where('success', true)->count();
        $failed   = collect($results)->where('success', false)->count();

        // Update qr_code_path di database untuk yang berhasil
        foreach ($results as $result) {
            if ($result['success']) {
                AttendanceStudent::where('nis', $result['nis'])
                    ->update(['qr_code_path' => $result['path']]);
            }
        }

        $message = "Berhasil generate {$success} QR Code.";
        if ($failed > 0) {
            $message .= " Gagal: {$failed} siswa.";
            return redirect()->back()->with('warning', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Generate PDF dengan kartu QR untuk distribusi ke siswa.
     *
     * POST /attendance/qr/cards-pdf
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateCardsPDF(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');

        // Validate input
        $validated = $request->validate([
            'class_id' => 'nullable|exists:attendance_classes,id',
            'layout' => 'in:3x3,4x4,6x6',
            'include_class' => 'boolean',
        ]);

        $classId = $validated['class_id'] ?? null;
        $layout = $validated['layout'] ?? '3x3';
        $includeClass = $validated['include_class'] ?? false;

        // Get students
        $query = AttendanceStudent::where('is_active', true);
        
        if ($classId) {
            $query->where('kelas_id', $classId);
            $kelas = AttendanceClass::find($classId);
            $className = $kelas ? $kelas->nama_kelas : 'AllClasses';
        } else {
            $className = 'Semua';
        }

        $students = $query
            ->with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('nis')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada siswa aktif untuk di-generate.');
        }

        // Ensure all students have QR codes
        foreach ($students as $student) {
            if (!$student->qr_code_path || !file_exists(storage_path('app/public/' . $student->qr_code_path))) {
                $path = $this->qrCodeService->generateQRCode($student->nis);
                $student->update(['qr_code_path' => $path]);
                $student->refresh();
            }
        }

        // Generate PDF
        try {
            // Convert to array with proper structure
            $studentsArray = $students->map(function($student) {
                $qrBase64 = null;
                
                // Convert SVG QR to PNG base64 for PDF compatibility
                if ($student->qr_code_path && file_exists(storage_path('app/public/' . $student->qr_code_path))) {
                    $qrPath = storage_path('app/public/' . $student->qr_code_path);
                    
                    if (str_ends_with($qrPath, '.svg')) {
                        // Convert SVG to PNG using imagick or gd
                        try {
                            if (extension_loaded('imagick')) {
                                $imagick = new \Imagick();
                                $imagick->setBackgroundColor('white');
                                $imagick->readImage($qrPath);
                                $imagick->setImageFormat('png');
                                $qrBase64 = base64_encode($imagick->getImageBlob());
                            } else {
                                // Fallback: read SVG as data URI
                                $svg = file_get_contents($qrPath);
                                $qrBase64 = base64_encode($svg);
                            }
                        } catch (\Exception $e) {
                            // If conversion fails, leave it null
                            $qrBase64 = null;
                        }
                    } else if (file_exists($qrPath)) {
                        // If already PNG or other format
                        $qrBase64 = base64_encode(file_get_contents($qrPath));
                    }
                }
                
                return [
                    'nis' => $student->nis,
                    'nama' => $student->nama,
                    'qr_code_path' => $student->qr_code_path,
                    'qr_code_base64' => $qrBase64,
                    'kelas' => $student->kelas ? [
                        'nama_kelas' => $student->kelas->nama_kelas
                    ] : null,
                ];
            })->toArray();

            $pdf = $this->qrCardPdfService->generatePDF(
                $studentsArray,
                $layout,
                $includeClass,
                config('app.school_name', 'SMK SPMB')
            );
            
            $filename = "QR_Kartu_Siswa_{$className}_" . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
