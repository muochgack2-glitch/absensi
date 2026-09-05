<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;

class StudentPhoneController extends Controller
{
    /**
     * Lookup siswa by NIS — untuk pre-fill form.
     * GET /api/phone/lookup?nis=12345
     */
    public function lookup(Request $request)
    {
        $nis = trim($request->input('nis', ''));

        if (empty($nis)) {
            return response()->json([
                'success' => false,
                'message' => 'NIS wajib diisi',
            ], 422);
        }

        $student = AttendanceStudent::with('kelas')
            ->where('nis', $nis)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NIS tersebut tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success'      => true,
            'nama'         => $student->nama,
            'nis'          => $student->nis,
            'kelas'        => $student->kelas?->nama_kelas ?? '-',
            'no_hp_ortu'   => $student->no_hp_ortu ?? '',
            'no_hp_ortu2'  => $student->no_hp_ortu2 ?? '',
        ]);
    }

    /**
     * Update nomor HP orang tua siswa by NIS.
     * POST /api/phone/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'nis'          => 'required|string',
            'no_hp_ortu'   => 'required|string|max:20',
            'no_hp_ortu2'  => 'nullable|string|max:20',
        ]);

        $student = AttendanceStudent::where('nis', $request->nis)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NIS tersebut tidak ditemukan',
            ], 404);
        }

        // Normalisasi: 08xxx → 628xxx
        $normalizePhone = function (?string $no): ?string {
            if (empty($no)) return null;
            $no = preg_replace('/\D/', '', $no); // hanya digit
            if (str_starts_with($no, '0'))      $no = '62' . substr($no, 1);
            elseif (str_starts_with($no, '8'))  $no = '62' . $no;
            return $no;
        };

        $student->no_hp_ortu  = $normalizePhone($request->no_hp_ortu);
        $student->no_hp_ortu2 = $normalizePhone($request->no_hp_ortu2);
        $student->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Nomor HP berhasil diperbarui',
            'nama'        => $student->nama,
            'no_hp_ortu'  => $student->no_hp_ortu,
            'no_hp_ortu2' => $student->no_hp_ortu2,
        ]);
    }

    /**
     * Handle OPTIONS preflight untuk CORS.
     */
    public function options()
    {
        return response()->json([], 200)
            ->header('Access-Control-Allow-Origin', config('services.phone_api.allowed_origin', '*'))
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-API-Key');
    }
}
