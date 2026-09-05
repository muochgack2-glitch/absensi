<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\Api\AttendanceStatsController;
use App\Http\Controllers\AttendanceSSEController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Api\EkaldikController;
use App\Http\Controllers\Api\StudentPhoneController;

// AJAX Login API (dengan session middleware untuk auth)
Route::middleware(['web'])->post('/auth/login', [AuthenticatedSessionController::class, 'store']);

// Attendance Scan API (untuk scanner frontend)
Route::prefix('attendance')->group(function () {
    Route::post('/scan', [AttendanceScanController::class, 'scan']);
    Route::post('/reject', [AttendanceScanController::class, 'reject']);
    
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
    Route::get('/lookup',  [StudentPhoneController::class, 'lookup']);   // cari siswa by NIS
    Route::post('/update', [StudentPhoneController::class, 'update']);   // update HP
});
