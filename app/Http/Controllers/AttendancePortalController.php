<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\AttendanceStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendancePortalController extends Controller
{
    /**
     * Halaman utama portal — form input NIS
     */
    public function index()
    {
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
        return view('attendance.portal.index', compact('schoolName'));
    }

    /**
     * Proses cari siswa berdasarkan NIS
     */
    public function check(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|min:3|max:20',
        ], [
            'nis.required' => 'NIS siswa wajib diisi.',
            'nis.min'      => 'NIS minimal 3 karakter.',
        ]);

        $nis = trim($request->input('nis'));

        $student = AttendanceStudent::with('kelas')
            ->where('nis', $nis)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors(['nis' => "Siswa dengan NIS \"{$nis}\" tidak ditemukan atau tidak aktif."]);
        }

        return redirect()->route('portal.result', ['nis' => $nis]);
    }

    /**
     * Halaman hasil — rekap absensi siswa
     */
    public function result(Request $request)
    {
        $nis = $request->query('nis');

        $student = AttendanceStudent::with('kelas')
            ->where('nis', $nis)
            ->where('is_active', true)
            ->firstOrFail();

        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');

        // Default: 30 hari terakhir atau bulan yang dipilih
        $bulan = $request->query('bulan', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($bulan)->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($bulan)->endOfMonth()->format('Y-m-d');

        $records = AttendanceRecord::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Summary
        $summary = [
            'hadir'     => $records->where('status', 'hadir')->count(),
            'terlambat' => $records->where('status', 'terlambat')->count(),
            'izin'      => $records->where('status', 'izin')->count(),
            'sakit'     => $records->where('status', 'sakit')->count(),
            'alpha'     => $records->where('status', 'alpha')->count(),
        ];

        // Total hari sekolah di bulan ini (sampai hari ini)
        $today    = Carbon::today();
        $endCalc  = Carbon::parse($endDate)->gt($today) ? $today : Carbon::parse($endDate);
        $totalHari = 0;
        $d = Carbon::parse($startDate);
        while ($d->lte($endCalc)) {
            if ($d->isWeekday()) $totalHari++;
            $d->addDay();
        }

        $persen = $totalHari > 0
            ? round((($summary['hadir'] + $summary['terlambat']) / $totalHari) * 100, 1)
            : 0;

        // Daftar bulan untuk filter (6 bulan terakhir)
        $bulanList = [];
        for ($i = 0; $i < 6; $i++) {
            $m = Carbon::now()->subMonths($i);
            $bulanList[] = [
                'value' => $m->format('Y-m'),
                'label' => $m->translatedFormat('F Y'),
            ];
        }

        return view('attendance.portal.result', compact(
            'student', 'schoolName', 'records', 'summary',
            'persen', 'totalHari', 'bulan', 'bulanList', 'startDate', 'endDate'
        ));
    }
}
