<?php

use App\Http\Controllers\AttendanceDashboardController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\AttendanceStudentController;
use App\Http\Controllers\AttendanceClassController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceQRController;
use App\Http\Controllers\AttendanceSettingController;
use App\Http\Controllers\AttendanceManualController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\WhatsAppGatewayController;
use App\Http\Controllers\WhatsAppDiagnosticController;
use App\Http\Controllers\StudentCardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Scanner Landing Page (no auth required)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes
require __DIR__.'/auth.php';

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('attendance.dashboard');
    })->name('dashboard');

    // Attendance Dashboard
    Route::get('/attendance/dashboard', [AttendanceDashboardController::class, 'index'])
        ->name('attendance.dashboard');
    
    Route::get('/attendance/dashboard/refresh', [AttendanceDashboardController::class, 'refresh'])
        ->name('attendance.dashboard.refresh');
    
    // API endpoint for sidebar badge
    Route::get('/api/attendance/today-stats', [AttendanceDashboardController::class, 'todayStats'])
        ->name('api.attendance.today-stats');

    // QR Scanner
    Route::get('/attendance/scanner', [AttendanceScanController::class, 'showScanner'])
        ->name('attendance.scanner');
    
    Route::post('/attendance/scan', [AttendanceScanController::class, 'scan'])
        ->name('attendance.scan');

    // Dashboard Chart API (AJAX)
    Route::get('/attendance/dashboard/chart-data', [AttendanceDashboardController::class, 'chartApi'])
        ->name('attendance.dashboard.chart-data');

    // Input Absensi Manual
    Route::get('/attendance/manual', [AttendanceManualController::class, 'index'])
        ->name('attendance.manual.index');
    Route::post('/attendance/manual', [AttendanceManualController::class, 'store'])
        ->name('attendance.manual.store');
    Route::delete('/attendance/manual/{record}', [AttendanceManualController::class, 'destroy'])
        ->name('attendance.manual.destroy');

    // Students Management - Custom routes BEFORE resource (prevent {student} catching)
    Route::get('/attendance/students/card', [StudentCardController::class, 'index'])
        ->name('attendance.students.card');
    Route::post('/attendance/students/card/generate', [StudentCardController::class, 'generate'])
        ->name('attendance.students.card.generate');
    
    Route::get('/attendance/students/import/form', [AttendanceStudentController::class, 'importForm'])
        ->name('attendance.students.import.form');
    
    Route::post('/attendance/students/import', [AttendanceStudentController::class, 'import'])
        ->name('attendance.students.import');
    
    Route::get('/attendance/students/export/template', [AttendanceStudentController::class, 'exportTemplate'])
        ->name('attendance.students.export.template');

    // Export Excel siswa (HARUS sebelum resource agar tidak tertangkap {student})
    Route::get('/attendance/students/export/excel', [AttendanceStudentController::class, 'exportExcel'])
        ->name('attendance.students.export.excel');

    // Bulk action siswa (HARUS sebelum resource)
    Route::post('/attendance/students/bulk-action', [AttendanceStudentController::class, 'bulkAction'])
        ->name('attendance.students.bulk-action');

    Route::get('/attendance/students/{student}/print-qr', [StudentCardController::class, 'printSingle'])
        ->name('attendance.students.print-qr');

    Route::resource('attendance/students', AttendanceStudentController::class)
        ->names('attendance.students');

    // Classes Management
    Route::resource('attendance/classes', AttendanceClassController::class)
        ->names('attendance.classes');

    // Reports
    Route::get('/attendance/reports', [AttendanceReportController::class, 'index'])
        ->name('attendance.reports.index');
    
    Route::get('/attendance/reports/daily', [AttendanceReportController::class, 'daily'])
        ->name('attendance.reports.daily');
    
    Route::get('/attendance/reports/monthly', [AttendanceReportController::class, 'monthly'])
        ->name('attendance.reports.monthly');
    
    Route::get('/attendance/reports/student/{student}', [AttendanceReportController::class, 'studentHistory'])
        ->name('attendance.reports.student');
    
    Route::post('/attendance/reports/generate', [AttendanceReportController::class, 'generate'])
        ->name('attendance.reports.generate');
    
    Route::get('/attendance/reports/export-summary', [AttendanceReportController::class, 'exportSummary'])
        ->name('attendance.reports.export-summary');

    // Laporan Alpha (siswa sering tidak hadir)
    Route::get('/attendance/reports/alpha', [AttendanceReportController::class, 'alphaReport'])
        ->name('attendance.reports.alpha');
    Route::post('/attendance/reports/alpha/notify', [AttendanceReportController::class, 'sendAlphaNotification'])
        ->name('attendance.reports.alpha.notify');

    // Export PDF
    Route::get('/attendance/reports/monthly/pdf', [AttendanceReportController::class, 'exportMonthlyPdf'])
        ->name('attendance.reports.monthly.pdf');
    Route::get('/attendance/reports/daily/pdf', [AttendanceReportController::class, 'exportDailyPdf'])
        ->name('attendance.reports.daily.pdf');

    // Export Excel monthly
    Route::get('/attendance/reports/monthly/excel', [AttendanceReportController::class, 'exportMonthlyExcel'])
        ->name('attendance.reports.monthly.excel');

    // Rekap Semester
    Route::get('/attendance/reports/semester', [AttendanceReportController::class, 'semester'])
        ->name('attendance.reports.semester');
    Route::get('/attendance/reports/semester/pdf', [AttendanceReportController::class, 'exportSemesterPdf'])
        ->name('attendance.reports.semester.pdf');
    Route::get('/attendance/reports/semester/excel', [AttendanceReportController::class, 'exportSemesterExcel'])
        ->name('attendance.reports.semester.excel');


    // QR Code Display
    Route::get('/attendance/qr/{student}', [AttendanceQRController::class, 'show'])
        ->name('attendance.qr.show');
    
    Route::get('/attendance/qr/{student}/download', [AttendanceQRController::class, 'download'])
        ->name('attendance.qr.download');
    
    Route::post('/attendance/qr/{student}/regenerate', [AttendanceQRController::class, 'regenerate'])
        ->name('attendance.qr.regenerate');

    Route::post('/attendance/qr/bulk-generate', [AttendanceQRController::class, 'bulkGenerate'])
        ->name('attendance.qr.bulk-generate');

    // Settings
    Route::get('/attendance/settings', [AttendanceSettingController::class, 'index'])
        ->name('attendance.settings.index');
    
    Route::put('/attendance/settings', [AttendanceSettingController::class, 'update'])
        ->name('attendance.settings.update');
    
    Route::post('/attendance/settings/reset', [AttendanceSettingController::class, 'reset'])
        ->name('attendance.settings.reset');
    
    Route::post('/attendance/settings/test-notification', [AttendanceSettingController::class, 'testNotification'])
        ->name('attendance.settings.test-notification');

    Route::get('/attendance/settings/backup', [AttendanceSettingController::class, 'downloadBackup'])
        ->name('attendance.settings.backup');

    Route::post('/attendance/settings/restore', [AttendanceSettingController::class, 'restoreBackup'])
        ->name('attendance.settings.restore');

    // ==========================================
    // WhatsApp Gateway Management
    // ==========================================
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        // Dashboard
        Route::get('/', [WhatsAppController::class, 'index'])->name('index');
        Route::get('/status', [WhatsAppController::class, 'status'])->name('status');
        Route::get('/health', [WhatsAppController::class, 'health'])->name('health');
        Route::get('/qr', [WhatsAppController::class, 'qrCode'])->name('qr');

        // Diagnostics & Auto-Fix
        Route::get('/diagnostics', [WhatsAppController::class, 'diagnostics'])->name('diagnostics');
        Route::post('/auto-fix', [WhatsAppController::class, 'autoFix'])->name('auto-fix');
        Route::post('/diagnostic/test-send', [WhatsAppDiagnosticController::class, 'testSend'])->name('diagnostic.test-send');

        // Send Messages
        Route::get('/send', [WhatsAppController::class, 'sendPage'])->name('send');
        Route::post('/send', [WhatsAppController::class, 'send'])->name('send.submit');
        Route::post('/send-template', [WhatsAppController::class, 'sendWithTemplate'])->name('send.template');

        // Logs
        Route::get('/logs', [WhatsAppController::class, 'logs'])->name('logs');

        // Templates CRUD
        Route::get('/templates', [WhatsAppController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [WhatsAppController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [WhatsAppController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{id}/edit', [WhatsAppController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{id}', [WhatsAppController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{id}', [WhatsAppController::class, 'deleteTemplate'])->name('templates.delete');

        // Settings
        Route::get('/settings', [WhatsAppController::class, 'settings'])->name('settings');
        Route::post('/settings', [WhatsAppController::class, 'updateSettings'])->name('settings.update');

        // Broadcast
        Route::get('/broadcast', [WhatsAppController::class, 'broadcastPage'])->name('broadcast');
        Route::post('/broadcast', [WhatsAppController::class, 'sendBroadcast'])->name('broadcast.submit');

        // Gateway Control
        Route::post('/logout', [WhatsAppController::class, 'logout'])->name('logout');
        Route::post('/restart', [WhatsAppController::class, 'restart'])->name('restart');
        Route::post('/reset', [WhatsAppController::class, 'reset'])->name('reset');
    });

    // Gateway Management (Dual Gateway)
    Route::prefix('gateway')->name('gateway.')->group(function () {
        Route::get('/', [WhatsAppGatewayController::class, 'index'])->name('index');
        Route::get('/statuses', [WhatsAppGatewayController::class, 'getStatuses'])->name('statuses');
        Route::get('/{gateway}/qr', [WhatsAppGatewayController::class, 'getQRCode'])->name('qr');
        Route::post('/{gateway}/restart', [WhatsAppGatewayController::class, 'restart'])->name('restart');
        Route::post('/{gateway}/logout', [WhatsAppGatewayController::class, 'logout'])->name('logout');
        Route::post('/{gateway}/reset', [WhatsAppGatewayController::class, 'resetGateway'])->name('reset');
        Route::get('/{gateway}/logs', [WhatsAppGatewayController::class, 'getLogs'])->name('logs');
        Route::post('/toggle-failover', [WhatsAppGatewayController::class, 'toggleFailover'])->name('toggle-failover');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
