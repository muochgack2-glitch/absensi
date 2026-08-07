<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WaliKelasController extends Controller
{
    // ================================================================
    // ADMIN — Manajemen akun Wali Kelas
    // ================================================================

    public function userIndex()
    {
        $users   = User::with('kelas')->orderBy('role')->orderBy('name')->get();
        $classes = AttendanceClass::where('is_active', true)->orderBy('nama_kelas')->get();
        return view('attendance.wali.admin-users', compact('users', 'classes'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,wali_kelas',
            'kelas_id' => 'nullable|exists:attendance_classes,id',
        ], [
            'email.unique'    => 'Email sudah digunakan.',
            'password.min'    => 'Password minimal 6 karakter.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'kelas_id' => $request->role === 'wali_kelas' ? $request->kelas_id : null,
        ]);

        return back()->with('success', "✅ Akun \"{$request->name}\" berhasil dibuat.");
    }

    public function userUpdate(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,wali_kelas',
            'kelas_id' => 'nullable|exists:attendance_classes,id',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'kelas_id' => $request->role === 'wali_kelas' ? $request->kelas_id : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "✅ Akun \"{$user->name}\" berhasil diperbarui.");
    }

    public function userDestroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Akun \"{$name}\" berhasil dihapus.");
    }

    // ================================================================
    // WALI KELAS — Dashboard khusus kelas sendiri
    // ================================================================

    public function waliDashboard()
    {
        $user  = Auth::user();
        $kelas = $user->kelas;

        if (!$kelas) {
            return view('attendance.wali.no-class');
        }

        $today     = Carbon::today()->format('Y-m-d');
        $students  = AttendanceStudent::where('kelas_id', $kelas->id)
                        ->where('is_active', true)->count();

        // Rekap hari ini
        $todayRecords = AttendanceRecord::whereHas('student', fn($q) => $q->where('kelas_id', $kelas->id))
            ->where('date', $today)->get();

        $todayStats = [
            'hadir'     => $todayRecords->where('status', 'hadir')->count(),
            'terlambat' => $todayRecords->where('status', 'terlambat')->count(),
            'izin'      => $todayRecords->where('status', 'izin')->count(),
            'sakit'     => $todayRecords->where('status', 'sakit')->count(),
            'alpha'     => $students - $todayRecords->count(),
        ];

        // Rekap 7 hari terakhir per hari
        $chartDays  = [];
        $chartHadir = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $recs = AttendanceRecord::whereHas('student', fn($q) => $q->where('kelas_id', $kelas->id))
                ->where('date', $d->format('Y-m-d'))->get();
            $chartDays[]  = $d->format('d/m');
            $chartHadir[] = $recs->whereIn('status', ['hadir', 'terlambat'])->count();
        }

        // Daftar siswa + rekap bulan ini
        $bulan     = Carbon::now()->format('Y-m');
        $startDate = Carbon::parse($bulan)->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($bulan)->endOfMonth()->format('Y-m-d');

        $siswaList = AttendanceStudent::where('kelas_id', $kelas->id)
            ->where('is_active', true)->orderBy('nama')->get()
            ->map(function ($s) use ($startDate, $endDate) {
                $recs = AttendanceRecord::where('student_id', $s->id)
                    ->whereBetween('date', [$startDate, $endDate])->get();
                $s->hadir     = $recs->where('status', 'hadir')->count();
                $s->terlambat = $recs->where('status', 'terlambat')->count();
                $s->alpha     = $recs->where('status', 'alpha')->count();
                return $s;
            });

        return view('attendance.wali.dashboard', compact(
            'kelas', 'students', 'todayStats', 'chartDays', 'chartHadir',
            'siswaList', 'bulan'
        ));
    }
}
