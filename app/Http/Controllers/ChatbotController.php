<?php

namespace App\Http\Controllers;

use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Get daily attendance summary for wali kelas by phone number
     * 
     * @param Request $request
     * @param string $phone Phone number in format 628xxx or 08xxx
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary(Request $request, $phone)
    {
        // Guard sederhana: endpoint ini tidak butuh login (dipanggil dari n8n),
        // tapi tetap butuh API key supaya tidak bisa diakses publik begitu saja
        // (mengembalikan nama & data absensi siswa berdasarkan nomor HP wali
        // kelas). Set CHATBOT_API_KEY di .env, kirim via header X-API-Key.
        $expectedKey = config('services.chatbot.api_key');
        if ($expectedKey && $request->header('X-API-Key') !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        try {
            // Normalize phone number (handle both 62xxx and 08xxx format)
            $normalizedPhone = $this->normalizePhone($phone);
            
            // Find wali kelas by phone number
            $waliKelas = User::where('phone', $normalizedPhone)
                            ->orWhere('phone', $phone)
                            ->where('role', 'wali_kelas')
                            ->first();
            
            if (!$waliKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor tidak terdaftar sebagai wali kelas. Silakan hubungi admin untuk mendaftarkan nomor Anda.'
                ], 404);
            }
            
            // Find class assigned to this wali kelas
            $kelas = AttendanceClass::where('id', $waliKelas->kelas_id)->first();
            
            if (!$kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum ditugaskan sebagai wali kelas. Silakan hubungi admin.'
                ], 404);
            }
            
            // Get today's date
            $today = Carbon::today()->toDateString();
            
            // Get all students in this class
            $students = AttendanceStudent::where('kelas_id', $kelas->id)->get();
            $totalSiswa = $students->count();
            
            // Get attendance records for today.
            // Catatan: pakai kolom 'date' (tanggal absensi), BUKAN 'created_at'
            // (kapan record dibuat) - keduanya bisa beda untuk input manual
            // yang di-backfill ke tanggal lampau.
            $attendanceToday = AttendanceRecord::where('date', $today)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
            
            // Count by status. Enum status di DB: hadir, terlambat, alpha, izin, sakit.
            // Masing-masing dihitung terpisah (bukan digabung) supaya
            // hadir + terlambat + sakit + izin + alpha = total_siswa persis.
            $hadir      = $attendanceToday->where('status', 'hadir')->count();
            $terlambat  = $attendanceToday->where('status', 'terlambat')->count();
            $sakit      = $attendanceToday->where('status', 'sakit')->count();
            $izin       = $attendanceToday->where('status', 'izin')->count();

            // Alpha = siswa dengan status eksplisit 'alpha' (biasanya di-set oleh
            // scheduled job attendance:mark-absent) DITAMBAH siswa yang sama sekali
            // belum punya record hari ini (misal job belum jalan). Sebelumnya kode
            // ini cuma menghitung "tidak ada record sama sekali", jadi begitu job
            // mark-absent jalan (buat record status=alpha), siswa itu malah hilang
            // dari hitungan alpha karena sudah "punya record".
            $alphaEksplisit   = $attendanceToday->where('status', 'alpha')->count();
            $studentIdsAdaRecord = $attendanceToday->pluck('student_id');
            $tanpaRecordSamaSekali = $totalSiswa - $studentIdsAdaRecord->count();
            $alpha = $alphaEksplisit + $tanpaRecordSamaSekali;
            
            // Daftar siswa yang tidak hadir = tanpa record sama sekali ATAU
            // record eksplisit berstatus alpha.
            $studentIdsAlphaEksplisit = $attendanceToday
                ->where('status', 'alpha')
                ->pluck('student_id');
            $idsTidakHadir = $students->pluck('id')
                ->diff($studentIdsAdaRecord)
                ->merge($studentIdsAlphaEksplisit)
                ->unique();

            $tidakHadir = $students->whereIn('id', $idsTidakHadir)
                ->map(function($student) {
                    return [
                        'nis'  => $student->nis,
                        'nama' => $student->nama,
                    ];
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'wali_kelas_nama' => $waliKelas->name,
                    'kelas_nama' => $kelas->nama_kelas,
                    'tanggal' => Carbon::parse($today)->locale('id')->isoFormat('DD MMMM YYYY'),
                    'total_siswa' => $totalSiswa,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                    'tidak_hadir' => $tidakHadir,
                    'nomor_wa' => $phone,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Chatbot getSummary error: ' . $e->getMessage(), [
                'phone' => $phone,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verifikasi kode pendaftaran dari wali kelas & simpan nomor/LID pengirim
     * sebagai phone user tersebut. Dipanggil dari n8n saat pesan masuk cocok
     * pola kode 6 digit (dicek dulu oleh workflow n8n / whatsapp-server
     * sebelum masuk ke sini).
     *
     * @param Request $request Body JSON: { "phone": "...", "code": "123456" }
     */
    public function verify(Request $request)
    {
        $expectedKey = config('services.chatbot.api_key');
        if ($expectedKey && $request->header('X-API-Key') !== $expectedKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        // Catatan: 'phone' di sini dikirim apa adanya dari whatsapp-server —
        // bisa berupa nomor asli (62xxx) ATAU LID (angka panjang ≥15 digit).
        // Sengaja TIDAK dipanggil normalizePhone() di sini, karena LID bukan
        // nomor telepon dan normalisasi 62-prefix justru akan merusaknya.
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $code  = preg_replace('/[^0-9]/', '', $request->code);

        $user = User::where('verification_code', $code)
                    ->where('role', 'wali_kelas')
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid atau sudah tidak berlaku. Hubungi admin untuk kode baru.',
            ], 404);
        }

        // Cegah 1 nomor/LID dipakai lebih dari 1 akun wali kelas
        $konflik = User::where('phone', $phone)
                        ->where('id', '!=', $user->id)
                        ->exists();
        if ($konflik) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor ini sudah terdaftar untuk wali kelas lain. Hubungi admin.',
            ], 409);
        }

        $user->update([
            'phone'             => $phone,
            'verification_code' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "✅ Pendaftaran berhasil! Nomor Anda terhubung ke akun wali kelas {$user->name}. Ketik \"ringkasan\" untuk melihat data absensi.",
        ]);
    }

    /**
     * Normalize phone number to consistent format (62xxx)
     * 
     * @param string $phone
     * @return string
     */
    private function normalizePhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}
