<?php

namespace App\Services;

use App\Models\AttendanceClass;
use App\Models\AttendancePromotion;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\AttendanceStudent;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TahunAjaranService
{
    /**
     * Buat tahun ajaran baru dan arsipkan tahun lama.
     */
    public function createNewYear(string $tahunBaru, array $options = []): array
    {
        DB::beginTransaction();
        try {
            if (!preg_match('/^\d{4}\/\d{4}$/', $tahunBaru)) {
                throw new \Exception('Format tahun ajaran harus YYYY/YYYY (contoh: 2027/2028)');
            }

            if (TahunAjaran::where('tahun', $tahunBaru)->exists()) {
                throw new \Exception("Tahun ajaran {$tahunBaru} sudah ada");
            }

            $currentYear = TahunAjaran::getActive();

            if ($options['archive_current'] ?? true) {
                TahunAjaran::where('tahun', $currentYear)->update([
                    'status'    => 'archived',
                    'closed_at' => now(),
                ]);
            }

            $newTA = TahunAjaran::create([
                'tahun'      => $tahunBaru,
                'status'     => 'active',
                'started_at' => now(),
                'created_by' => auth()->id(),
            ]);

            AttendanceSetting::set('active_tahun_ajaran', $tahunBaru, 'system');
            AttendanceSetting::clearCache();

            DB::commit();

            Log::info('Tahun ajaran baru dibuat', [
                'tahun_baru' => $tahunBaru,
                'tahun_lama' => $currentYear,
                'user'       => auth()->user()?->name,
            ]);

            return [
                'success' => true,
                'message' => "Tahun ajaran {$tahunBaru} berhasil dibuat. Tahun {$currentYear} diarsipkan.",
                'tahun_ajaran' => $newTA,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal buat tahun ajaran baru', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    // ================================================================
    // PREVIEW — Adopsi dari E-Kaldik
    // ================================================================

    /**
     * Generate preview data naik kelas (tanpa eksekusi).
     * Sama seperti E-Kaldik: generatePreviewData()
     */
    public function generatePreview(string $tahunLama, string $tahunBaru, array $mapping = []): array
    {
        if (empty($mapping)) {
            $mapping = $this->buildAutoMapping();
        }

        $students = AttendanceStudent::withoutGlobalScope('tahun_ajaran')
            ->with('kelas')
            ->where('tahun_ajaran', $tahunLama)
            ->where('is_active', true)
            ->get();

        $preview = [];
        $totalPromoted  = 0;
        $totalGraduated = 0;

        // Group students by kelas
        $groupedByKelas = $students->groupBy('kelas_id');

        foreach ($groupedByKelas as $kelasId => $kelasStudents) {
            $kelas = $kelasStudents->first()->kelas;
            $studentCount = $kelasStudents->count();

            if (!$kelas) continue;

            if (!isset($mapping[$kelasId])) {
                // Kelas XII → Lulus
                $preview[] = [
                    'source_class'    => $kelas->nama_kelas,
                    'source_class_id' => $kelas->id,
                    'source_tingkat'  => $kelas->tingkat,
                    'jurusan'         => $kelas->jurusan,
                    'student_count'   => $studentCount,
                    'target'          => 'LULUS / ALUMNI',
                    'target_class'    => null,
                    'target_class_id' => null,
                    'action'          => 'graduate',
                ];
                $totalGraduated += $studentCount;
            } else {
                // Naik kelas
                $targetKelas = AttendanceClass::find($mapping[$kelasId]);

                $preview[] = [
                    'source_class'    => $kelas->nama_kelas,
                    'source_class_id' => $kelas->id,
                    'source_tingkat'  => $kelas->tingkat,
                    'jurusan'         => $kelas->jurusan,
                    'student_count'   => $studentCount,
                    'target'          => $targetKelas?->tingkat,
                    'target_class'    => $targetKelas?->nama_kelas,
                    'target_class_id' => $targetKelas?->id,
                    'action'          => 'promote',
                ];
                $totalPromoted += $studentCount;
            }
        }

        return [
            'from_year'       => $tahunLama,
            'to_year'         => $tahunBaru,
            'items'           => $preview,
            'total_promoted'  => $totalPromoted,
            'total_graduated' => $totalGraduated,
            'total_students'  => $totalPromoted + $totalGraduated,
        ];
    }

    // ================================================================
    // PROCESS — Adopsi dari E-Kaldik processPromotion()
    // ================================================================

    /**
     * Naik kelas massal + simpan detail per siswa untuk rollback.
     */
    public function naikKelas(string $tahunLama, string $tahunBaru, array $mapping = [], string $notes = ''): array
    {
        DB::beginTransaction();
        try {
            if (empty($mapping)) {
                $mapping = $this->buildAutoMapping();
            }

            $students = AttendanceStudent::withoutGlobalScope('tahun_ajaran')
                ->with('kelas')
                ->where('tahun_ajaran', $tahunLama)
                ->where('is_active', true)
                ->get();

            $promoted  = 0;
            $graduated = 0;
            $skipped   = 0;
            $summary        = [];
            $studentDetails = []; // Untuk rollback (pola E-Kaldik)

            foreach ($students as $student) {
                $oldKelasId = $student->kelas_id;

                if (!isset($mapping[$oldKelasId])) {
                    // ── LULUS ──
                    $studentDetails[] = [
                        'student_id'        => $student->id,
                        'nis'               => $student->nis,
                        'nama'              => $student->nama,
                        'previous_kelas_id' => $student->kelas_id,
                        'previous_kelas'    => $student->kelas?->nama_kelas,
                        'action'            => 'graduate',
                    ];

                    $student->update(['is_active' => false]);
                    $graduated++;
                    continue;
                }

                $newKelasId = $mapping[$oldKelasId];

                // Cek duplikat
                $exists = AttendanceStudent::withoutGlobalScope('tahun_ajaran')
                    ->where('nis', $student->nis)
                    ->where('tahun_ajaran', $tahunBaru)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // ── NAIK KELAS ──
                $newKelas = AttendanceClass::find($newKelasId);

                $studentDetails[] = [
                    'student_id'        => $student->id,
                    'nis'               => $student->nis,
                    'nama'              => $student->nama,
                    'previous_kelas_id' => $student->kelas_id,
                    'previous_kelas'    => $student->kelas?->nama_kelas,
                    'new_kelas_id'      => $newKelasId,
                    'new_kelas'         => $newKelas?->nama_kelas,
                    'action'            => 'promote',
                    'new_student_id'    => null, // Akan diisi setelah create
                ];

                $newStudent = AttendanceStudent::create([
                    'nis'          => $student->nis,
                    'nama'         => $student->nama,
                    'kelas_id'     => $newKelasId,
                    'no_hp_ortu'   => $student->no_hp_ortu,
                    'qr_code_path' => $student->qr_code_path,
                    'foto_profil'  => $student->foto_profil,
                    'is_active'    => true,
                    'tahun_ajaran' => $tahunBaru,
                ]);

                // Update detail dengan ID baru
                $studentDetails[count($studentDetails) - 1]['new_student_id'] = $newStudent->id;
                $promoted++;
            }

            // Build summary per kelas
            $groupedDetails = collect($studentDetails)->groupBy('previous_kelas');
            foreach ($groupedDetails as $kelas => $details) {
                $summary[] = [
                    'source' => $kelas,
                    'target' => $details->first()['action'] === 'graduate'
                        ? 'LULUS'
                        : ($details->first()['new_kelas'] ?? '-'),
                    'count'  => $details->count(),
                ];
            }

            // Simpan record promosi (pola E-Kaldik)
            AttendancePromotion::create([
                'from_tahun_ajaran' => $tahunLama,
                'to_tahun_ajaran'   => $tahunBaru,
                'processed_by'      => auth()->id(),
                'total_promoted'    => $promoted,
                'total_graduated'   => $graduated,
                'promotion_summary' => $summary,
                'student_details'   => $studentDetails,
                'notes'             => $notes,
                'processed_at'      => now(),
            ]);

            DB::commit();

            $message = "Naik kelas selesai: {$promoted} siswa naik, {$graduated} siswa lulus, {$skipped} dilewati.";

            Log::info('Naik kelas massal', [
                'tahun_lama' => $tahunLama,
                'tahun_baru' => $tahunBaru,
                'promoted'   => $promoted,
                'graduated'  => $graduated,
                'skipped'    => $skipped,
            ]);

            return [
                'success'   => true,
                'message'   => $message,
                'promoted'  => $promoted,
                'graduated' => $graduated,
                'skipped'   => $skipped,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal naik kelas', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal naik kelas: ' . $e->getMessage()];
        }
    }

    // ================================================================
    // ROLLBACK — Adopsi dari E-Kaldik rollbackPromotion()
    // ================================================================

    /**
     * Undo/rollback naik kelas — kembalikan siswa ke keadaan sebelumnya.
     */
    public function rollbackPromotion(int $promotionId): array
    {
        DB::beginTransaction();
        try {
            $promotion = AttendancePromotion::findOrFail($promotionId);

            if (!$promotion->canRollback()) {
                throw new \Exception('Promosi ini tidak dapat di-undo. Hanya promosi terakhir yang bisa di-undo.');
            }

            if (empty($promotion->student_details)) {
                throw new \Exception('Tidak ada data tracking siswa untuk undo.');
            }

            $restored = 0;

            foreach ($promotion->student_details as $detail) {
                if ($detail['action'] === 'graduate') {
                    // Kembalikan siswa lulus → aktif lagi
                    $student = AttendanceStudent::withoutGlobalScope('tahun_ajaran')
                        ->find($detail['student_id']);

                    if ($student) {
                        $student->update(['is_active' => true]);
                        $restored++;
                    }
                } elseif ($detail['action'] === 'promote') {
                    // Hapus siswa copy di tahun baru
                    if (!empty($detail['new_student_id'])) {
                        AttendanceStudent::withoutGlobalScope('tahun_ajaran')
                            ->where('id', $detail['new_student_id'])
                            ->delete();
                    }
                    $restored++;
                }
            }

            // Tandai promosi sebagai rolled back
            $promotion->update([
                'is_rolled_back'  => true,
                'rolled_back_at'  => now(),
                'rolled_back_by'  => auth()->id(),
            ]);

            DB::commit();

            Log::info('Rollback naik kelas', [
                'promotion_id' => $promotionId,
                'restored'     => $restored,
            ]);

            return [
                'success' => true,
                'message' => "Undo berhasil! {$restored} siswa dikembalikan ke keadaan sebelumnya.",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal rollback naik kelas', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal undo: ' . $e->getMessage()];
        }
    }

    // ================================================================
    // HELPERS
    // ================================================================

    /**
     * Build auto mapping: kelas tingkat X → XI, XI → XII, XII → lulus
     */
    private function buildAutoMapping(): array
    {
        $classes = AttendanceClass::where('is_active', true)->get();
        $mapping = [];

        foreach ($classes as $kelas) {
            $nextTingkat = match ($kelas->tingkat) {
                '10' => '11', '11' => '12', '12' => null,
                'X'  => 'XI', 'XI' => 'XII', 'XII' => null,
                default => null,
            };

            if ($nextTingkat === null) continue;

            $nextKelas = $classes->first(function ($k) use ($nextTingkat, $kelas) {
                return $k->tingkat === $nextTingkat && $k->jurusan === $kelas->jurusan;
            });

            if ($nextKelas) {
                $mapping[$kelas->id] = $nextKelas->id;
            }
        }

        return $mapping;
    }

    /**
     * Ambil statistik per tahun ajaran.
     */
    public function getStatistics(string $tahunAjaran): array
    {
        return [
            'total_siswa'  => AttendanceStudent::withoutGlobalScope('tahun_ajaran')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('is_active', true)->count(),
            'total_record' => AttendanceRecord::withoutGlobalScope('tahun_ajaran')
                ->where('tahun_ajaran', $tahunAjaran)->count(),
            'total_hadir'  => AttendanceRecord::withoutGlobalScope('tahun_ajaran')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'hadir')->count(),
            'total_alpha'  => AttendanceRecord::withoutGlobalScope('tahun_ajaran')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', 'alpha')->count(),
        ];
    }
}
