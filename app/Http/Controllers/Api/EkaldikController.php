<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkaldikController extends Controller
{
    /**
     * Get attendance data for E-Kaldik journal integration.
     *
     * Returns scan status for given NIS array on a specific date.
     * Used by E-Kaldik to auto-fill student attendance in teaching journals.
     *
     * GET /api/ekaldik/attendance?date=2026-08-13&nis[]=001&nis[]=002
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'nis' => 'required|array|min:1',
            'nis.*' => 'required|string|max:50',
        ]);

        $date = $validated['date'];
        $nisArray = $validated['nis'];

        // Query attendance records for the given NIS list on the specified date
        // Global scope on AttendanceRecord auto-filters by active tahun_ajaran
        $records = AttendanceRecord::with('student')
            ->whereDate('date', $date)
            ->whereHas('student', function ($query) use ($nisArray) {
                $query->whereIn('nis', $nisArray);
            })
            ->get();

        // Build response keyed by NIS
        $data = [];
        foreach ($records as $record) {
            if ($record->student) {
                $data[$record->student->nis] = [
                    'status' => $record->status,
                    'check_in_time' => $record->check_in_time,
                    'check_out_time' => $record->check_out_time,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'date' => $date,
            'queried' => count($nisArray),
            'found' => count($data),
        ]);
    }
}
