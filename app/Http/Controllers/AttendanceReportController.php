<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateReportRequest;
use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\AttendanceSetting;
use App\Services\AttendanceExportService;
use App\Services\AttendanceNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    protected $exportService;
    protected $notificationService;

    public function __construct(
        AttendanceExportService $exportService,
        AttendanceNotificationService $notificationService
    ) {
        $this->exportService       = $exportService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display report generation form
     */
    public function index()
    {
        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('attendance.reports.index', compact('classes'));
    }

    /**
     * Generate and preview report
     */
    public function generate(GenerateReportRequest $request)
    {
        $validated = $request->validated();

        $query = AttendanceRecord::with(['student.kelas'])
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']]);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($validated) {
                $q->where('kelas_id', $validated['class_id']);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $validated['status']);
        }

        $records = $query->orderBy('date', 'asc')
            ->orderBy('check_in_time', 'asc')
            ->get();

        // If preview, return view
        if ($validated['format'] === 'preview') {
            $summary = [
                'total_records' => $records->count(),
                'hadir' => $records->where('status', 'hadir')->count(),
                'terlambat' => $records->where('status', 'terlambat')->count(),
                'sakit' => $records->where('status', 'sakit')->count(),
                'izin' => $records->where('status', 'izin')->count(),
                'alpha' => $records->where('status', 'alpha')->count(),
            ];

            $classes = AttendanceClass::where('is_active', true)
                ->orderBy('tingkat', 'asc')
                ->orderBy('nama_kelas', 'asc')
                ->get();

            return view('attendance.reports.preview', compact(
                'records',
                'summary',
                'validated',
                'classes'
            ));
        }

        // If excel export
        if ($validated['format'] === 'excel') {
            return $this->exportService->exportToExcel([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'class_id' => $validated['class_id'] ?? null,
                'status' => $validated['status'] ?? null,
            ]);
        }

        return back()->with('error', 'Format laporan tidak didukung.');
    }

    /**
     * Export summary report (per class)
     */
    public function exportSummary(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return $this->exportService->exportSummaryToExcel([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);
    }

    /**
     * Daily attendance report
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $classId = $request->input('class_id');

        $query = AttendanceRecord::with(['student.kelas'])
            ->whereDate('date', $date);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('kelas_id', $classId);
            });
        }

        $records = $query->orderBy('check_in_time', 'asc')->get();

        // Get students who haven't checked in
        $studentsQuery = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->whereDoesntHave('attendanceRecords', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });

        if ($classId) {
            $studentsQuery->where('kelas_id', $classId);
        }

        $absentStudents = $studentsQuery->get();

        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('attendance.reports.daily', compact(
            'records',
            'absentStudents',
            'classes',
            'date',
            'classId'
        ));
    }

    /**
     * Monthly summary report
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $classId = $request->input('class_id');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Get all students
        $studentsQuery = AttendanceStudent::with('kelas')
            ->where('is_active', true);

        if ($classId) {
            $studentsQuery->where('kelas_id', $classId);
        }

        $students = $studentsQuery->get();

        // Get attendance records for the month
        $records = AttendanceRecord::whereBetween('date', [$startDate, $endDate])
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Calculate statistics per student
        $summary = $students->map(function ($student) use ($records) {
            $studentRecords = $records->get($student->id, collect());

            return [
                'student' => $student,
                'hadir' => $studentRecords->where('status', 'hadir')->count(),
                'terlambat' => $studentRecords->where('status', 'terlambat')->count(),
                'sakit' => $studentRecords->where('status', 'sakit')->count(),
                'izin' => $studentRecords->where('status', 'izin')->count(),
                'alpha' => $studentRecords->where('status', 'alpha')->count(),
                'total' => $studentRecords->count(),
            ];
        });

        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('attendance.reports.monthly', compact(
            'summary',
            'classes',
            'month',
            'classId'
        ));
    }

    /**
     * Student attendance history
     */
    public function studentHistory($studentId)
    {
        $student = AttendanceStudent::with('kelas')->findOrFail($studentId);

        $records = AttendanceRecord::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->paginate(30);

        $stats = [
            'hadir' => AttendanceRecord::where('student_id', $studentId)->where('status', 'hadir')->count(),
            'terlambat' => AttendanceRecord::where('student_id', $studentId)->where('status', 'terlambat')->count(),
            'sakit' => AttendanceRecord::where('student_id', $studentId)->where('status', 'sakit')->count(),
            'izin' => AttendanceRecord::where('student_id', $studentId)->where('status', 'izin')->count(),
            'alpha' => AttendanceRecord::where('student_id', $studentId)->where('status', 'alpha')->count(),
        ];

        return view('attendance.reports.student-history', compact('student', 'records', 'stats'));
    }

    // ================================================================
    // LAPORAN ALPHA — Siswa Paling Sering Tidak Hadir
    // ================================================================

    public function alphaReport(Request $request)
    {
        $month   = $request->input('month', Carbon::now()->format('Y-m'));
        $classId = $request->input('class_id');
        $minAlpha = (int) $request->input('min_alpha', 1);

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate   = Carbon::parse($month)->endOfMonth();

        $query = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->withCount(['attendanceRecords as alpha_count' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->where('status', 'alpha');
            }])
            ->having('alpha_count', '>=', $minAlpha)
            ->orderByDesc('alpha_count');

        if ($classId) {
            $query->where('kelas_id', $classId);
        }

        $students = $query->get();
        $classes  = AttendanceClass::where('is_active', true)
            ->orderBy('nama_kelas')->get();

        return view('attendance.reports.alpha', compact(
            'students', 'classes', 'month', 'classId', 'minAlpha'
        ));
    }

    public function sendAlphaNotification(Request $request)
    {
        $validated = $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:attendance_students,id',
        ]);

        $month     = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $success   = 0;
        $failed    = 0;

        foreach ($validated['student_ids'] as $id) {
            $student = AttendanceStudent::with('kelas')->find($id);
            if (!$student || empty($student->no_hp_ortu)) { $failed++; continue; }

            $alphaCount = AttendanceRecord::where('student_id', $id)
                ->whereMonth('date', $startDate->month)
                ->whereYear('date', $startDate->year)
                ->where('status', 'alpha')->count();

            // Kirim WA custom dengan info alpha bulan ini
            $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
            $message    = "🏫 *{$schoolName}*\n";
            $message   .= "⚠️ *Laporan Ketidakhadiran Bulan " . Carbon::parse($month)->translatedFormat('F Y') . "*\n\n";
            $message   .= "Siswa: *{$student->nama}*\n";
            $message   .= "Kelas: {$student->kelas->nama_kelas}\n";
            $message   .= "Total Alpha bulan ini: *{$alphaCount} hari*\n\n";
            $message   .= "Mohon segera menghubungi pihak sekolah.\n";
            $message   .= "\n_Pesan otomatis dari sistem absensi_";

            $whatsapp = app(\App\Services\AttendanceWhatsAppService::class);
            $result   = $whatsapp->sendParentNotification($student->no_hp_ortu, $message);
            $result['success'] ? $success++ : $failed++;
        }

        return back()->with('success', "Notifikasi WA terkirim: ✓ {$success} berhasil, ✗ {$failed} gagal.");
    }

    // ================================================================
    // EXPORT PDF
    // ================================================================

    public function exportMonthlyPdf(Request $request)
    {
        $month   = $request->input('month', Carbon::now()->format('Y-m'));
        $classId = $request->input('class_id');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate   = Carbon::parse($month)->endOfMonth();

        $studentsQuery = AttendanceStudent::with('kelas')->where('is_active', true);
        if ($classId) $studentsQuery->where('kelas_id', $classId);
        $students = $studentsQuery->get();

        $records = AttendanceRecord::whereBetween('date', [$startDate, $endDate])
            ->whereIn('student_id', $students->pluck('id'))
            ->get()->groupBy('student_id');

        $summary = $students->map(function ($student) use ($records) {
            $r = $records->get($student->id, collect());
            return [
                'student'   => $student,
                'hadir'     => $r->where('status', 'hadir')->count(),
                'terlambat' => $r->where('status', 'terlambat')->count(),
                'sakit'     => $r->where('status', 'sakit')->count(),
                'izin'      => $r->where('status', 'izin')->count(),
                'alpha'     => $r->where('status', 'alpha')->count(),
                'total'     => $r->count(),
            ];
        });

        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
        $className  = $classId
            ? AttendanceClass::find($classId)?->nama_kelas
            : 'Semua Kelas';

        $pdf = Pdf::loadView('attendance.reports.pdf-monthly', compact(
            'summary', 'month', 'schoolName', 'className'
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan-bulanan-' . $month . '.pdf';
        return $pdf->download($filename);
    }

    public function exportDailyPdf(Request $request)
    {
        $date    = $request->input('date', Carbon::today()->format('Y-m-d'));
        $classId = $request->input('class_id');

        $query = AttendanceRecord::with(['student.kelas'])->whereDate('date', $date);
        if ($classId) {
            $query->whereHas('student', fn($q) => $q->where('kelas_id', $classId));
        }
        $records = $query->orderBy('check_in_time')->get();

        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
        $className  = $classId
            ? AttendanceClass::find($classId)?->nama_kelas
            : 'Semua Kelas';

        $pdf = Pdf::loadView('attendance.reports.pdf-daily', compact(
            'records', 'date', 'schoolName', 'className'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-harian-' . $date . '.pdf';
        return $pdf->download($filename);
    }

    // ================================================================
    // EXPORT EXCEL BULANAN
    // ================================================================

    public function exportMonthlyExcel(Request $request)
    {
        $month   = $request->input('month', Carbon::now()->format('Y-m'));
        $classId = $request->input('class_id');

        return $this->exportService->exportToExcel([
            'start_date' => Carbon::parse($month)->startOfMonth()->format('Y-m-d'),
            'end_date'   => Carbon::parse($month)->endOfMonth()->format('Y-m-d'),
            'class_id'   => $classId,
        ]);
    }
}
