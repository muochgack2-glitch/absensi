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
     * Get attendance data for last 7 weekdays
     */
    private function getChartData7Days(): array
    {
        $days   = [];
        $labels = [];
        $hadir  = [];
        $alpha  = [];
        $terlambat = [];

        $date = Carbon::today();
        $collected = 0;

        // Ambil 7 hari ke belakang (skip hari Minggu)
        while ($collected < 7) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $records = AttendanceRecord::whereDate('date', $date)->get();
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
}

