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
            'phone'    => 'nullable|string|regex:/^[0-9]{9,13}$/|unique:users,phone',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,wali_kelas,petugas',
            'kelas_id' => 'nullable|exists:attendance_classes,id',
        ], [
            'email.unique'    => 'Email sudah digunakan.',
            'phone.regex'     => 'Format nomor WhatsApp tidak valid (8xxxxxxxxx).',
            'phone.unique'    => 'Nomor WhatsApp sudah terdaftar.',
            'password.min'    => 'Password minimal 6 karakter.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
        ]);

        // Normalize phone: add 62 prefix if provided
        $phone = $request->phone ? '62' . $request->phone : null;

        // Kalau nomor WA belum diisi manual & role wali_kelas, generate kode
        // verifikasi 6 digit supaya wali kelas bisa daftar sendiri via chatbot
        // WA (perlu buat kasus nomor terdeteksi sebagai LID, bukan nomor asli).
        $verificationCode = (!$phone && $request->role === 'wali_kelas')
            ? $this->generateVerificationCode()
            : null;

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $phone,
            'verification_code' => $verificationCode,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'kelas_id'          => $request->role === 'wali_kelas' ? $request->kelas_id : null,
        ]);

        return back()->with('success', "✅ Akun \"{$request->name}\" berhasil dibuat.");
    }

    public function userUpdate(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|regex:/^[0-9]{9,13}$/|unique:users,phone,' . $user->id,
            'role'     => 'required|in:admin,wali_kelas,petugas',
            'kelas_id' => 'nullable|exists:attendance_classes,id',
            'password' => 'nullable|string|min:6',
        ], [
            'phone.regex'  => 'Format nomor WhatsApp tidak valid (8xxxxxxxxx).',
            'phone.unique' => 'Nomor WhatsApp sudah terdaftar.',
        ]);

        // Normalize phone: add 62 prefix if provided
        $phone = $request->phone ? '62' . $request->phone : null;

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $phone,
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

    /**
     * Generate ulang kode verifikasi WA untuk wali kelas (misal karena nomor
     * lama sudah tidak bisa dipakai, atau kode sebelumnya lupa/hilang).
     * Ini mengosongkan phone yang sudah terdaftar (kalau ada) supaya wali
     * kelas bisa daftar ulang dari nomor WA manapun pakai kode baru.
     */
    public function userRegenerateCode(User $user)
    {
        if ($user->role !== 'wali_kelas') {
            return back()->with('error', 'Kode verifikasi hanya untuk akun wali kelas.');
        }

        $user->update([
            'phone'             => null,
            'verification_code' => $this->generateVerificationCode(),
        ]);

        return back()->with('success', "🔑 Kode verifikasi baru untuk \"{$user->name}\": {$user->verification_code}");
    }

    private function generateVerificationCode(): string
    {
        do {
            $code = (string) random_int(100000, 999999);
        } while (User::where('verification_code', $code)->exists());

        return $code;
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
