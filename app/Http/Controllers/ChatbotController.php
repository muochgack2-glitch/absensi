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
     * @param string $phone Phone number in format 628xxx or 08xxx
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary($phone)
    {
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
            
            // Get attendance records for today
            $attendanceToday = AttendanceRecord::whereDate('created_at', $today)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
            
            // Count by status
            $hadir = $attendanceToday->where('status', 'hadir')->count();
            $sakit = $attendanceToday->where('status', 'sakit')->count();
            $izin = $attendanceToday->where('status', 'izin')->count();
            $alpha = $totalSiswa - $attendanceToday->count();
            
            // Get students who haven't attended
            $studentIdsPresent = $attendanceToday->pluck('student_id');
            $tidakHadir = $students->whereNotIn('id', $studentIdsPresent)
                ->map(function($student) {
                    return [
                        'nis' => $student->nis,
                        'nama' => $student->name
                    ];
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'wali_kelas_nama' => $waliKelas->name,
                    'kelas_nama' => $kelas->name,
                    'tanggal' => Carbon::parse($today)->locale('id')->isoFormat('DD MMMM YYYY'),
                    'total_siswa' => $totalSiswa,
                    'hadir' => $hadir,
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
