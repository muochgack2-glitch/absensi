<?php

namespace App\Http\Controllers;

use App\Models\AttendanceStudent;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceQRController extends Controller
{
    public function __construct(
        private QRCodeService $qrCodeService
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
