<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceManualController extends Controller
{
    /**
     * Form input absensi manual — tampilkan daftar siswa berdasarkan kelas & tanggal.
     */
    public function index(Request $request)
    {
        $date    = $request->input('date', Carbon::today()->format('Y-m-d'));
        $classId = $request->input('class_id');

        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat')->orderBy('nama_kelas')->get();

        $students = collect();
        $records  = collect();

        if ($classId) {
            $students = AttendanceStudent::with('kelas')
                ->where('kelas_id', $classId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            // Ambil record yang sudah ada untuk tanggal ini
            $records = AttendanceRecord::whereDate('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id'); // key by student_id agar mudah dicari
        }

        return view('attendance.manual.index', compact(
            'date', 'classId', 'classes', 'students', 'records'
        ));
    }

    /**
     * Simpan atau update absensi manual untuk banyak siswa sekaligus.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date'              => 'required|date',
            'entries'           => 'required|array',
            'entries.*.student_id' => 'required|exists:attendance_students,id',
            'entries.*.status'  => 'required|in:hadir,terlambat,izin,sakit,alpha,skip',
        ]);

        $date    = $request->input('date');
        $entries = $request->input('entries', []);
        $saved   = 0;
        $skipped = 0;

        foreach ($entries as $entry) {
            // "skip" = tidak ubah / biarkan kosong
            if ($entry['status'] === 'skip') {
                $skipped++;
                continue;
            }

            $checkInTime = !empty($entry['check_in_time'])
                ? $entry['check_in_time']
                : ($entry['status'] === 'hadir' || $entry['status'] === 'terlambat'
                    ? Carbon::parse($date)->setTimeFromTimeString('07:00')->format('H:i:s')
                    : null);

            AttendanceRecord::updateOrCreate(
                [
                    'student_id' => $entry['student_id'],
                    'date'       => $date,
                ],
                [
                    'status'        => $entry['status'],
                    'check_in_time' => $checkInTime,
                    'notes'         => $entry['notes'] ?? null,
                ]
            );

            $saved++;
        }

        return redirect()
            ->route('attendance.manual.index', [
                'date'     => $date,
                'class_id' => $request->input('class_id'),
            ])
            ->with('success', "✅ {$saved} absensi disimpan." . ($skipped ? " {$skipped} dilewati." : ''));
    }

    /**
     * Hapus satu record absensi (untuk koreksi).
     */
    public function destroy(AttendanceRecord $record)
    {
        $date    = $record->date->format('Y-m-d');
        $classId = $record->student->kelas_id;

        $record->delete();

        return redirect()
            ->route('attendance.manual.index', ['date' => $date, 'class_id' => $classId])
            ->with('success', 'Record absensi berhasil dihapus.');
    }
}
