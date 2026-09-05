<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use Illuminate\Http\Request;

class StudentPhoneController extends Controller
{
    /**
     * Daftar kelas aktif (yang punya siswa).
     * GET /api/phone/classes
     */
    public function classes()
    {
        $classes = AttendanceClass::withCount(['students' => fn($q) => $q->where('is_active', true)])
            ->having('students_count', '>', 0)
            ->orderBy('nama_kelas')
            ->get(['id', 'nama_kelas']);

        return response()->json([
            'success' => true,
            'data'    => $classes->map(fn($k) => [
                'id'         => $k->id,
                'nama_kelas' => $k->nama_kelas,
                'jumlah'     => $k->students_count,
            ]),
        ]);
    }

    /**
     * Siswa per kelas beserta nomor HP.
     * GET /api/phone/students?kelas_id=1
     */
    public function students(Request $request)
    {
        $kelasId = $request->input('kelas_id');

        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'kelas_id wajib diisi'], 422);
        }

        $kelas = AttendanceClass::find($kelasId);
        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $students = AttendanceStudent::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'no_hp_ortu', 'no_hp_ortu2']);

        return response()->json([
            'success'    => true,
            'kelas'      => $kelas->nama_kelas,
            'kelas_id'   => $kelas->id,
            'jumlah'     => $students->count(),
            'data'       => $students->map(fn($s) => [
                'id'          => $s->id,
                'nama'        => $s->nama,
                'nis'         => $s->nis,
                'no_hp_ortu'  => $s->no_hp_ortu  ?? '',
                'no_hp_ortu2' => $s->no_hp_ortu2 ?? '',
            ]),
        ]);
    }

    /**
     * Bulk update HP seluruh siswa dalam satu kelas.
     * POST /api/phone/bulk-update
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'students'              => 'required|array|min:1',
            'students.*.id'         => 'required|integer',
            'students.*.no_hp_ortu' => 'nullable|string|max:20',
            'students.*.no_hp_ortu2'=> 'nullable|string|max:20',
        ]);

        $normalize = function (?string $no): ?string {
            if (empty(trim((string) $no))) return null;
            $no = preg_replace('/\D/', '', $no);
            if (str_starts_with($no, '0'))     $no = '62' . substr($no, 1);
            elseif (str_starts_with($no, '8')) $no = '62' . $no;
            return $no ?: null;
        };

        $updated = 0;
        foreach ($request->students as $item) {
            $rows = AttendanceStudent::where('id', $item['id'])
                ->where('is_active', true)
                ->update([
                    'no_hp_ortu'  => $normalize($item['no_hp_ortu']  ?? null),
                    'no_hp_ortu2' => $normalize($item['no_hp_ortu2'] ?? null),
                ]);
            $updated += $rows;
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} data siswa berhasil disimpan",
            'updated' => $updated,
        ]);
    }

    /**
     * Lookup siswa by NIS — untuk pre-fill form.
     * GET /api/phone/lookup?nis=12345
     */
    public function lookup(Request $request)
    {
        $nis = trim($request->input('nis', ''));

        if (empty($nis)) {
            return response()->json(['success' => false, 'message' => 'NIS wajib diisi'], 422);
        }

        $student = AttendanceStudent::with('kelas')
            ->where('nis', $nis)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Siswa dengan NIS tersebut tidak ditemukan'], 404);
        }

        return response()->json([
            'success'     => true,
            'nama'        => $student->nama,
            'nis'         => $student->nis,
            'kelas'       => $student->kelas?->nama_kelas ?? '-',
            'no_hp_ortu'  => $student->no_hp_ortu  ?? '',
            'no_hp_ortu2' => $student->no_hp_ortu2 ?? '',
        ]);
    }

    /**
     * Update nomor HP orang tua siswa by NIS.
     * POST /api/phone/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'nis'         => 'required|string',
            'no_hp_ortu'  => 'required|string|max:20',
            'no_hp_ortu2' => 'nullable|string|max:20',
        ]);

        $student = AttendanceStudent::where('nis', $request->nis)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Siswa dengan NIS tersebut tidak ditemukan'], 404);
        }

        $normalize = function (?string $no): ?string {
            if (empty($no)) return null;
            $no = preg_replace('/\D/', '', $no);
            if (str_starts_with($no, '0'))     $no = '62' . substr($no, 1);
            elseif (str_starts_with($no, '8')) $no = '62' . $no;
            return $no;
        };

        $student->no_hp_ortu  = $normalize($request->no_hp_ortu);
        $student->no_hp_ortu2 = $normalize($request->no_hp_ortu2);
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
