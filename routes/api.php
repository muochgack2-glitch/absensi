<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\Api\AttendanceStatsController;
use App\Http\Controllers\AttendanceSSEController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Api\EkaldikController;
use App\Http\Controllers\Api\StudentPhoneController;
use App\Http\Controllers\TrustedDeviceController;
use App\Http\Controllers\Api\WaAckController;

// WhatsApp ACK webhook — dipanggil gateway Baileys saat status centang berubah
Route::post('/wa-ack', [WaAckController::class, 'receive']);

// AJAX Login API (dengan session middleware untuk auth)
Route::middleware(['web'])->post('/auth/login', [AuthenticatedSessionController::class, 'store']);

// Trusted Device API
Route::prefix('device')->group(function () {
    // Validasi token device (publik, dipanggil saat welcome page load)
    Route::post('/validate', [TrustedDeviceController::class, 'validate']);
    // Register device baru (butuh session auth — login dulu)
    Route::middleware(['web'])->post('/register', [TrustedDeviceController::class, 'register']);
});

// Admin: Kelola trusted devices (butuh login + role admin/petugas)
Route::middleware(['web', 'auth', 'role:admin,petugas'])->prefix('devices')->group(function () {
    Route::get('/', [TrustedDeviceController::class, 'index']);
    Route::post('/{device}/revoke', [TrustedDeviceController::class, 'revoke']);
    Route::post('/{device}/activate', [TrustedDeviceController::class, 'activate']);
    Route::delete('/{device}', [TrustedDeviceController::class, 'destroy']);
});

// Attendance Scan API (untuk scanner frontend)
// Scan & reject butuh perangkat terpercaya (trusted device)
Route::prefix('attendance')->group(function () {
    Route::post('/scan', [AttendanceScanController::class, 'scan'])
        ->middleware(['trusted.device', 'throttle:60,1']);
    Route::post('/reject', [AttendanceScanController::class, 'reject'])
        ->middleware(['trusted.device']);
    
    // Stats API for public landing page
    Route::get('/stats/today', [AttendanceStatsController::class, 'todayStats']);
    Route::get('/school-hours', [AttendanceStatsController::class, 'schoolHours']);
    Route::get('/recent-scans', [AttendanceStatsController::class, 'recentScans']);
    Route::get('/live-data', [AttendanceStatsController::class, 'liveData']);
    
    // SSE for real-time updates
    Route::get('/sse', [AttendanceSSEController::class, 'stream']);
});

// Announcement API
Route::get('/announcement/active', [AttendanceStatsController::class, 'activeAnnouncement']);

// Chatbot API - for n8n WhatsApp chatbot integration
Route::prefix('chatbot')->group(function () {
    Route::get('/summary/{phone}', [ChatbotController::class, 'getSummary']);
    Route::post('/verify', [ChatbotController::class, 'verify']);
});

// E-Kaldik Integration API - untuk auto-fill absensi di jurnal mengajar
Route::middleware(['ekaldik.api'])->prefix('ekaldik')->group(function () {
    Route::get('/attendance', [EkaldikController::class, 'getAttendance']);
});

// Phone Update API - untuk form update HP ortu dari domain wa.dmcenter.my.id
// OPTIONS preflight (CORS) — tanpa middleware auth
Route::options('/phone/{any}', [StudentPhoneController::class, 'options'])->where('any', '.*');
Route::middleware(['phone.api'])->prefix('phone')->group(function () {
    Route::get('/classes',       [StudentPhoneController::class, 'classes']);       // daftar kelas
    Route::get('/students',      [StudentPhoneController::class, 'students']);      // siswa per kelas
    Route::get('/students/all',  [StudentPhoneController::class, 'allStudents']);   // semua siswa (untuk import)
    Route::post('/bulk-update',  [StudentPhoneController::class, 'bulkUpdate']);    // simpan semua
    Route::get('/lookup',        [StudentPhoneController::class, 'lookup']);        // cari by NIS
    Route::post('/update',       [StudentPhoneController::class, 'update']);        // update 1 siswa
});

