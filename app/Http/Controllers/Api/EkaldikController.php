<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceIzin;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkaldikController extends Controller
{
    /**
     * Get attendance data for E-Kaldik journal integration.
     *
     * Returns status for given NIS array on a specific date.
     * Sources (priority order):
     *   1. AttendanceRecord  — scan QR masuk (hadir/terlambat)
     *   2. AttendanceIzin    — surat izin / sakit (disetujui atau pending)
     *   3. Tidak ada record  — alpha (tidak hadir)
     *
     * GET /api/ekaldik/attendance?date=2026-09-04&nis[]=001&nis[]=002
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'  => 'required|date',
            'nis'   => 'required|array|min:1',
            'nis.*' => 'required|string|max:50',
        ]);

        $date     = $validated['date'];
        $nisArray = $validated['nis'];

        // ── 1. QR scan records ─────────────────────────────────────────────
        $scanRecords = AttendanceRecord::with('student')
            ->whereDate('date', $date)
            ->whereHas('student', fn($q) => $q->whereIn('nis', $nisArray))
            ->get();

        $data = [];
        foreach ($scanRecords as $record) {
            if ($record->student) {
                $data[$record->student->nis] = [
                    'status'         => $record->status,
                    'check_in_time'  => $record->check_in_time,
                    'check_out_time' => $record->check_out_time,
                    'source'         => 'scan',
                ];
            }
        }

        // ── 2. Izin / Sakit — hanya untuk NIS yang belum ada scan ─────────
        $missingNis = array_values(array_diff($nisArray, array_keys($data)));

        if (!empty($missingNis)) {
            $izinRecords = AttendanceIzin::with('student')
                ->whereHas('student', fn($q) => $q->whereIn('nis', $missingNis))
                ->whereDate('tanggal_mulai', '<=', $date)   // DATE() wrapper — works on SQLite & MySQL
                ->whereDate('tanggal_selesai', '>=', $date)
                ->whereIn('status', ['disetujui', 'pending'])
                ->orderByRaw("CASE WHEN status = 'disetujui' THEN 0 ELSE 1 END")
                ->get();

            foreach ($izinRecords as $izin) {
                if (!$izin->student) continue;
                $nis = $izin->student->nis;
                if (!isset($data[$nis])) {
                    $data[$nis] = [
                        'status'          => $izin->jenis,   // 'izin' atau 'sakit'
                        'check_in_time'   => null,
                        'check_out_time'  => null,
                        'source'          => 'izin_' . $izin->status,
                        'alasan'          => $izin->alasan,
                        'tanggal_mulai'   => $izin->tanggal_mulai?->toDateString(),
                        'tanggal_selesai' => $izin->tanggal_selesai?->toDateString(),
                    ];
                }
            }
        }

        return response()->json([
            'success'   => true,
            'data'      => $data,
            'date'      => $date,
            'queried'   => count($nisArray),
            'found'     => count($data),
            'breakdown' => [
                'scan'  => collect($data)->where('source', 'scan')->count(),
                'izin'  => collect($data)->filter(fn($d) => str_starts_with($d['source'] ?? '', 'izin_'))->count(),
                'alpha' => count($nisArray) - count($data),
            ],
        ]);
    }
}
