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
     * Proses cari siswa berdasarkan NIS atau Nama
     */
    public function check(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
        ], [
            'query.required' => 'Masukkan NIS atau nama siswa.',
            'query.min'      => 'Minimal 2 karakter.',
        ]);

        $q = trim($request->input('query'));

        // Coba cari by NIS dulu (exact match)
        $byNis = AttendanceStudent::with('kelas')
            ->where('nis', $q)
            ->where('is_active', true)
            ->first();

        if ($byNis) {
            return redirect()->route('portal.result', ['nis' => $byNis->nis]);
        }

        // Cari by nama (partial, case-insensitive)
        $byNama = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->where('nama', 'LIKE', '%' . $q . '%')
            ->orderBy('kelas_id')
            ->orderBy('nama')
            ->get();

        if ($byNama->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['query' => "Siswa \"$q\" tidak ditemukan. Coba masukkan NIS atau nama yang lebih lengkap."]);
        }

        if ($byNama->count() === 1) {
            return redirect()->route('portal.result', ['nis' => $byNama->first()->nis]);
        }

        // Lebih dari 1 hasil — tampilkan halaman pilih siswa
        return view('attendance.portal.select', [
            'students'   => $byNama,
            'query'      => $q,
            'schoolName' => AttendanceSetting::get('school_name', 'Sekolah'),
        ]);
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
