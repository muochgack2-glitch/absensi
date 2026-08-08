<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceDashboardController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    /**
     * Display attendance dashboard
     */
    public function index(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedClass = $request->get('class', null);

        // Get statistics
        $stats = $this->attendanceService->getAttendanceStats($selectedDate, $selectedClass);

        // Get attendance records
        $attendanceRecords = $this->getAttendanceRecords($selectedDate, $selectedClass);

        // Get absent students
        $absentStudents = $this->getAbsentStudents($selectedDate, $selectedClass);

        // Get all active classes for filter
        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // Data chart 7 hari terakhir
        $chartData = $this->getChartData7Days();

        // Persentase per status hari ini
        $totalToday = collect($stats)->only(['hadir','terlambat','alpha','izin','sakit'])->sum();
        $donutData  = [
            'hadir'     => $stats['hadir']     ?? 0,
            'terlambat' => $stats['terlambat'] ?? 0,
            'alpha'     => $stats['alpha']     ?? 0,
            'izin'      => $stats['izin']      ?? 0,
            'sakit'     => $stats['sakit']     ?? 0,
        ];

        return view('attendance.dashboard.index', compact(
            'selectedDate',
            'selectedClass',
            'stats',
            'attendanceRecords',
            'absentStudents',
            'classes',
            'chartData',
            'donutData',
            'totalToday'
        ));
    }

    /**
     * API: Chart data untuk AJAX filter per kelas
     */
    public function chartApi(Request $request): \Illuminate\Http\JsonResponse
    {
        $classId = $request->input('class_id');
        return response()->json($this->getChartData7Days($classId));
    }

    /**
     * Get attendance data for last 7 weekdays, optional filter by class
     */
    private function getChartData7Days(?int $classId = null): array
    {
        $labels    = [];
        $hadir     = [];
        $alpha     = [];
        $terlambat = [];

        $date      = Carbon::today();
        $collected = 0;

        while ($collected < 7) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $query = AttendanceRecord::whereDate('date', $date);

                if ($classId) {
                    $query->whereHas('student', fn($q) => $q->where('kelas_id', $classId));
                }

                $records   = $query->get();
                $labels[]    = $date->translatedFormat('d M');
                $hadir[]     = $records->where('status', 'hadir')->count()
                             + $records->where('status', 'terlambat')->count();
                $alpha[]     = $records->where('status', 'alpha')->count();
                $terlambat[] = $records->where('status', 'terlambat')->count();
                $collected++;
            }
            $date->subDay();
        }

        return [
            'labels'    => array_reverse($labels),
            'hadir'     => array_reverse($hadir),
            'alpha'     => array_reverse($alpha),
            'terlambat' => array_reverse($terlambat),
        ];
    }

    /**
     * Get attendance records for display
     */
    private function getAttendanceRecords($date, $classId = null)
    {
        $query = AttendanceRecord::with(['student.kelas'])
            ->whereDate('date', $date);

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('kelas_id', $classId);
            });
        }

        return $query->orderBy('check_in_time', 'asc')->get();
    }

    /**
     * Get students who haven't checked in
     */
    private function getAbsentStudents($date, $classId = null)
    {
        $query = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->whereDoesntHave('attendanceRecords', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });

        if ($classId) {
            $query->where('kelas_id', $classId);
        }

        return $query->get();
    }

    /**
     * AJAX: Refresh dashboard data
     */
    public function refresh(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedClass = $request->get('class', null);

        $stats = $this->attendanceService->getAttendanceStats($selectedDate, $selectedClass);
        $attendanceRecords = $this->getAttendanceRecords($selectedDate, $selectedClass);
        $absentStudents = $this->getAbsentStudents($selectedDate, $selectedClass);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'attendanceRecords' => $attendanceRecords,
            'absentStudents' => $absentStudents,
        ]);
    }

    /**
     * API: Get today's stats for sidebar badge
     */
    public function todayStats()
    {
        $today = Carbon::today()->format('Y-m-d');
        $stats = $this->attendanceService->getAttendanceStats($today);

        return response()->json([
            'success' => true,
            'present' => $stats['present'] ?? 0,
            'late' => $stats['late'] ?? 0,
            'absent' => $stats['absent'] ?? 0,
            'total' => $stats['total_students'] ?? 0,
        ]);
    }

    /**
     * API: Get notification data for sidebar bell
     */
    public function notifications()
    {
        $pendingIzin = \App\Models\AttendanceIzin::where('status', 'pending')->count();
        
        $todayAlpha = AttendanceRecord::whereDate('date', Carbon::today())
            ->where('status', 'alpha')->count();
        
        $items = [];
        
        if ($pendingIzin > 0) {
            $items[] = [
                'icon' => 'fa-envelope',
                'color' => 'yellow',
                'text' => "{$pendingIzin} izin menunggu persetujuan",
                'url' => route('attendance.izin.index'),
            ];
        }
        
        if ($todayAlpha > 0) {
            $items[] = [
                'icon' => 'fa-user-times',
                'color' => 'red',
                'text' => "{$todayAlpha} siswa alpha hari ini",
                'url' => route('attendance.dashboard'),
            ];
        }

        // Recent izin (last 5)
        $recentIzin = \App\Models\AttendanceIzin::with('student')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        foreach ($recentIzin as $izin) {
            $items[] = [
                'icon' => 'fa-file-alt',
                'color' => 'blue',
                'text' => ($izin->student->nama ?? 'Siswa') . ' — ' . ucfirst($izin->jenis),
                'url' => route('attendance.izin.index'),
                'time' => $izin->created_at->diffForHumans(),
            ];
        }

        return response()->json([
            'success' => true,
            'total' => $pendingIzin + ($todayAlpha > 0 ? 1 : 0),
            'items' => $items,
        ]);
    }
}

