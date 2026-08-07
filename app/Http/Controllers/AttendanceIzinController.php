<?php

namespace App\Http\Controllers;

use App\Models\AttendanceIzin;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSetting;
use App\Models\AttendanceStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceIzinController extends Controller
{
    // ================================================================
    // PUBLIC — Form pengajuan izin (tanpa login)
    // ================================================================

    public function publicForm()
    {
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
        return view('attendance.izin.public-form', compact('schoolName'));
    }

    public function publicSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $q = trim($request->input('query'));

        // Cari by NIS exact atau nama partial
        $students = AttendanceStudent::with('kelas')
            ->where('is_active', true)
            ->where(function ($qu) use ($q) {
                $qu->where('nis', $q)
                   ->orWhere('nama', 'LIKE', "%$q%");
            })
            ->orderBy('nama')
            ->get();

        return response()->json($students->map(fn($s) => [
            'id'    => $s->id,
            'nis'   => $s->nis,
            'nama'  => $s->nama,
            'kelas' => $s->kelas->nama_kelas ?? '-',
        ]));
    }

    public function publicSubmit(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:attendance_students,id',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis'           => 'required|in:izin,sakit',
            'alasan'          => 'required|string|min:10|max:500',
            'nama_pelapor'    => 'required|string|max:100',
            'no_hp_pelapor'   => 'required|string|regex:/^08[0-9]{8,12}$/',
            'lampiran'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'alasan.min'                     => 'Alasan minimal 10 karakter.',
            'no_hp_pelapor.regex'            => 'Format nomor HP: 08XXXXXXXXXX',
            'lampiran.max'                   => 'Ukuran file maksimal 5MB.',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')
                ->store('izin-lampiran', 'public');
        }

        AttendanceIzin::create([
            'student_id'      => $request->student_id,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis'           => $request->jenis,
            'alasan'          => $request->alasan,
            'nama_pelapor'    => $request->nama_pelapor,
            'no_hp_pelapor'   => $request->no_hp_pelapor,
            'lampiran'        => $lampiranPath,
            'status'          => 'pending',
        ]);

        return back()->with('success', '✅ Pengajuan izin berhasil dikirim! Admin sekolah akan memproses permohonan Anda.');
    }

    // ================================================================
    // ADMIN — Manajemen izin
    // ================================================================

    public function adminIndex(Request $request)
    {
        $status  = $request->input('status', 'pending');
        $classId = $request->input('class_id');

        $query = AttendanceIzin::with(['student.kelas', 'approvedBy'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($classId, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('kelas_id', $classId)))
            ->orderBy('created_at', 'desc');

        $izinList  = $query->paginate(20);
        $classes   = \App\Models\AttendanceClass::where('is_active', true)->orderBy('nama_kelas')->get();
        $countPending = AttendanceIzin::where('status', 'pending')->count();

        return view('attendance.izin.admin-index', compact('izinList', 'classes', 'status', 'classId', 'countPending'));
    }

    public function approve(Request $request, AttendanceIzin $izin)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:255',
        ]);

        $izin->update([
            'status'        => 'disetujui',
            'catatan_admin' => $request->catatan_admin,
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
        ]);

        // Buat/update attendance record untuk setiap hari dalam range
        $d = Carbon::parse($izin->tanggal_mulai);
        while ($d->lte($izin->tanggal_selesai)) {
            if ($d->isWeekday()) {
                AttendanceRecord::updateOrCreate(
                    ['student_id' => $izin->student_id, 'date' => $d->format('Y-m-d')],
                    ['status' => $izin->jenis, 'notes' => 'Auto: izin online #' . $izin->id]
                );
            }
            $d->addDay();
        }

        return back()->with('success', "✅ Izin disetujui. Absensi {$izin->student->nama} otomatis diperbarui.");
    }

    public function reject(Request $request, AttendanceIzin $izin)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:255',
        ], [
            'catatan_admin.required' => 'Harap isi alasan penolakan.',
        ]);

        $izin->update([
            'status'        => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
        ]);

        return back()->with('success', "Pengajuan izin {$izin->student->nama} ditolak.");
    }
}
