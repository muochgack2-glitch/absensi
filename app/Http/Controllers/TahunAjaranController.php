<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendancePromotion;
use App\Models\AttendanceSetting;
use App\Models\TahunAjaran;
use App\Services\TahunAjaranService;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    protected TahunAjaranService $service;

    public function __construct(TahunAjaranService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman utama — daftar semua tahun ajaran + riwayat promosi
     */
    public function index()
    {
        $tahunList = TahunAjaran::orderByDesc('tahun')->get();
        $activeTahun = TahunAjaran::getActive();

        foreach ($tahunList as $ta) {
            $ta->stats = $this->service->getStatistics($ta->tahun);
        }

        $activeModel = $tahunList->firstWhere('status', 'active');
        $suggestNext = $activeModel?->suggestNextYear()
            ?? (now()->year . '/' . (now()->year + 1));

        $classes = AttendanceClass::where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // Riwayat promosi (E-Kaldik style)
        $promotions = AttendancePromotion::with(['processedBy', 'rolledBackBy'])
            ->orderByDesc('processed_at')
            ->get();

        // Siswa alumni/lulus (bypass global scope)
        $alumni = \App\Models\AttendanceStudent::withoutGlobalScope('tahun_ajaran')
            ->with('kelas')
            ->where('is_active', false)
            ->orderByDesc('updated_at')
            ->get();

        return view('attendance.tahun-ajaran.index', compact(
            'tahunList', 'activeTahun', 'suggestNext', 'classes', 'promotions', 'alumni'
        ));
    }

    /**
     * Buat tahun ajaran baru
     */
    public function create(Request $request)
    {
        $request->validate([
            'tahun' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'tahun.required' => 'Tahun ajaran wajib diisi.',
            'tahun.regex'    => 'Format harus YYYY/YYYY (contoh: 2027/2028).',
        ]);

        $result = $this->service->createNewYear($request->tahun, [
            'archive_current' => true,
        ]);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Aktifkan tahun ajaran tertentu (switch)
     */
    public function activate(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::where('id', '!=', $tahunAjaran->id)
            ->where('status', 'active')
            ->update(['status' => 'archived']);

        $tahunAjaran->update(['status' => 'active']);

        AttendanceSetting::set('active_tahun_ajaran', $tahunAjaran->tahun, 'system');
        AttendanceSetting::clearCache();

        return back()->with('success', "Tahun ajaran {$tahunAjaran->tahun} berhasil diaktifkan.");
    }

    /**
     * API: Preview naik kelas (AJAX) — E-Kaldik style
     */
    public function previewNaikKelas(Request $request)
    {
        $request->validate([
            'tahun_lama' => 'required|string',
            'tahun_baru' => 'required|string',
            'mapping'    => 'nullable|array',
        ]);

        $preview = $this->service->generatePreview(
            $request->tahun_lama,
            $request->tahun_baru,
            $request->mapping ?? []
        );

        return response()->json([
            'success' => true,
            'preview' => $preview,
        ]);
    }

    /**
     * Proses naik kelas massal
     */
    public function naikKelas(Request $request)
    {
        $request->validate([
            'tahun_lama' => 'required|string',
            'tahun_baru' => 'required|string',
            'mapping'    => 'nullable|array',
            'notes'      => 'nullable|string|max:500',
        ]);

        $result = $this->service->naikKelas(
            $request->tahun_lama,
            $request->tahun_baru,
            $request->mapping ?? [],
            $request->notes ?? ''
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Undo/rollback naik kelas — E-Kaldik style
     */
    public function rollback(Request $request)
    {
        $request->validate([
            'promotion_id' => 'required|exists:attendance_promotions,id',
        ]);

        $result = $this->service->rollbackPromotion($request->promotion_id);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
