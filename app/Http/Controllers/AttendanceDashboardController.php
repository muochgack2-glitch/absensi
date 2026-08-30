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

        // Top 5 siswa paling awal masuk minggu ini
        $startOfWeek   = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $isSqlite      = \DB::connection()->getDriverName() === 'sqlite';
        $timeToSec     = $isSqlite
            ? "(CAST(strftime('%H', check_in_time) AS INTEGER) * 3600 + CAST(strftime('%M', check_in_time) AS INTEGER) * 60 + CAST(strftime('%S', check_in_time) AS INTEGER))"
            : 'TIME_TO_SEC(check_in_time)';
        $topEarlyWeek  = AttendanceRecord::with('student.kelas')
            ->whereBetween('date', [$startOfWeek->toDateString(), Carbon::today()->toDateString()])
            ->whereNotNull('check_in_time')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->selectRaw("student_id, AVG({$timeToSec}) as avg_sec, MIN(check_in_time) as earliest, COUNT(*) as hari_hadir")
            ->groupBy('student_id')
            ->orderBy('avg_sec', 'asc')
            ->take(5)
            ->get();

        // Top 5 siswa paling awal masuk bulan ini
        $startOfMonth  = Carbon::now()->startOfMonth();
        $topEarlyMonth = AttendanceRecord::with('student.kelas')
            ->whereBetween('date', [$startOfMonth->toDateString(), Carbon::today()->toDateString()])
            ->whereNotNull('check_in_time')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->selectRaw("student_id, AVG({$timeToSec}) as avg_sec, MIN(check_in_time) as earliest, COUNT(*) as hari_hadir")
            ->groupBy('student_id')
            ->orderBy('avg_sec', 'asc')
            ->take(5)
            ->get();

        // Top 5 siswa paling sering alpha minggu ini
        $topAlphaWeek = AttendanceRecord::with('student.kelas')
            ->whereBetween('date', [$startOfWeek->toDateString(), Carbon::today()->toDateString()])
            ->where('status', 'alpha')
            ->selectRaw('student_id, COUNT(*) as jumlah_alpha')
            ->groupBy('student_id')
            ->orderBy('jumlah_alpha', 'desc')
            ->take(5)
            ->get();

        // Top 5 siswa paling sering alpha bulan ini
        $topAlphaMonth = AttendanceRecord::with('student.kelas')
            ->whereBetween('date', [$startOfMonth->toDateString(), Carbon::today()->toDateString()])
            ->where('status', 'alpha')
            ->selectRaw('student_id, COUNT(*) as jumlah_alpha')
            ->groupBy('student_id')
            ->orderBy('jumlah_alpha', 'desc')
            ->take(5)
            ->get();

        return view('attendance.dashboard.index', compact(
            'selectedDate',
            'selectedClass',
            'stats',
            'attendanceRecords',
            'absentStudents',
            'classes',
            'chartData',
            'donutData',
            'totalToday',
            'topEarlyWeek',
            'topEarlyMonth',
            'topAlphaWeek',
            'topAlphaMonth'
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
        $user  = auth()->user();
        $items = [];
        $total = 0;

        // Kepala Sekolah: hanya rekap kehadiran hari ini (tidak ada akses ke izin)
        if ($user?->isKepalaSekolah()) {
            $todayStats = AttendanceRecord::whereDate('date', Carbon::today())
                ->selectRaw('status, COUNT(*) as jumlah')
                ->groupBy('status')
                ->pluck('jumlah', 'status');

            $hadir     = ($todayStats['hadir']     ?? 0) + ($todayStats['terlambat'] ?? 0);
            $alpha     = $todayStats['alpha']      ?? 0;
            $sakit     = $todayStats['sakit']      ?? 0;
            $izinCount = $todayStats['izin']       ?? 0;

            $items[] = [
                'icon'  => 'fa-chart-bar',
                'color' => 'blue',
                'text'  => "Hari ini: {$hadir} hadir, {$alpha} alpha, {$sakit} sakit, {$izinCount} izin",
                'url'   => route('attendance.dashboard'),
            ];

            if ($alpha > 0) {
                $total++;
                $items[] = [
                    'icon'  => 'fa-user-times',
                    'color' => 'red',
                    'text'  => "{$alpha} siswa alpha hari ini",
                    'url'   => route('attendance.dashboard'),
                ];
            }

            return response()->json([
                'success' => true,
                'total'   => $total,
                'items'   => $items,
            ]);
        }

        // Waka, Admin, Petugas: notifikasi penuh termasuk izin
        $pendingIzin = \App\Models\AttendanceIzin::where('status', 'pending')->count();

        $todayAlpha = AttendanceRecord::whereDate('date', Carbon::today())
            ->where('status', 'alpha')->count();

        if ($pendingIzin > 0) {
            $total += $pendingIzin;
            $items[] = [
                'icon'  => 'fa-envelope',
                'color' => 'yellow',
                'text'  => "{$pendingIzin} izin menunggu persetujuan",
                'url'   => route('attendance.izin.index'),
            ];
        }

        if ($todayAlpha > 0) {
            $total++;
            $items[] = [
                'icon'  => 'fa-user-times',
                'color' => 'red',
                'text'  => "{$todayAlpha} siswa alpha hari ini",
                'url'   => route('attendance.dashboard'),
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
                'icon'  => 'fa-file-alt',
                'color' => 'blue',
                'text'  => ($izin->student->nama ?? 'Siswa') . ' — ' . ucfirst($izin->jenis),
                'url'   => route('attendance.izin.index'),
                'time'  => $izin->created_at->diffForHumans(),
            ];
        }

        return response()->json([
            'success' => true,
            'total'   => $total,
            'items'   => $items,
        ]);

    }
}

