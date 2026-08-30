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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendancePortalController;
use App\Http\Controllers\AttendanceIzinController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\StudentCardController;
use Illuminate\Support\Facades\Route;

// Public Scanner Landing Page (no auth required)
Route::get('/', function () {
    $useDualCamera        = \App\Models\AttendanceSetting::get('use_dual_camera', '0');
    $qrCameraIndex        = \App\Models\AttendanceSetting::get('qr_camera_index', '0');
    $photoCameraIndex     = \App\Models\AttendanceSetting::get('photo_camera_index', '1');
    $qrCameraDeviceId     = \App\Models\AttendanceSetting::get('qr_camera_deviceid', '');
    $photoCameraDeviceId  = \App\Models\AttendanceSetting::get('photo_camera_deviceid', '');
    $checkOutStartTime    = \App\Models\AttendanceSetting::get('check_out_start_time', '12:00');
    return view('welcome', compact('useDualCamera', 'qrCameraIndex', 'photoCameraIndex', 'qrCameraDeviceId', 'photoCameraDeviceId', 'checkOutStartTime'));
})->name('home');

// ==========================================
// Portal Publik Cek Absensi Ortu
// ==========================================
Route::get('/portal', [AttendancePortalController::class, 'index'])->name('portal.index');
Route::post('/portal/check', [AttendancePortalController::class, 'check'])
    ->middleware('throttle:10,1') // max 10 cek per menit per IP
    ->name('portal.check');
Route::get('/portal/result', [AttendancePortalController::class, 'result'])->name('portal.result');

// ==========================================
// Form Izin Online Publik
// ==========================================
Route::get('/izin', [AttendanceIzinController::class, 'publicForm'])->name('izin.form');
Route::get('/izin/search', [AttendanceIzinController::class, 'publicSearch'])
    ->middleware('throttle:15,1') // max 15 pencarian per menit per IP
    ->name('izin.search');
Route::post('/izin/submit', [AttendanceIzinController::class, 'publicSubmit'])
    ->middleware('throttle:3,1')  // max 3 submit izin per menit per IP
    ->name('izin.submit');

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

    // API endpoint for notification bell
    Route::get('/api/attendance/notifications', [AttendanceDashboardController::class, 'notifications'])
        ->name('api.attendance.notifications');

    // QR Scanner
    Route::get('/attendance/scanner', [AttendanceScanController::class, 'showScanner'])
        ->name('attendance.scanner');
    
    Route::post('/attendance/scan', [AttendanceScanController::class, 'scan'])
        ->name('attendance.scan');

    // Dashboard Chart API (AJAX)
    Route::get('/attendance/dashboard/chart-data', [AttendanceDashboardController::class, 'chartApi'])
        ->name('attendance.dashboard.chart-data');

    // Input Absensi Manual — Petugas, Waka, Admin (bukan Kepala Sekolah)
    Route::middleware('role:petugas')->group(function () {
        Route::get('/attendance/manual', [AttendanceManualController::class, 'index'])
            ->name('attendance.manual.index');
        Route::post('/attendance/manual', [AttendanceManualController::class, 'store'])
            ->name('attendance.manual.store');
        Route::delete('/attendance/manual/{record}', [AttendanceManualController::class, 'destroy'])
            ->name('attendance.manual.destroy');
    });

    // Students Management — URUTAN PENTING: route spesifik HARUS di atas /{student} wildcard!

    // 1. Admin + Waka: cetak kartu, export, bulk, print QR (spesifik — HARUS sebelum {student})
    Route::middleware('role:admin,waka_kesiswaan')->group(function () {
        Route::get('/attendance/students/card', [StudentCardController::class, 'index'])
            ->name('attendance.students.card');
        Route::post('/attendance/students/card/generate', [StudentCardController::class, 'generate'])
            ->name('attendance.students.card.generate');
        Route::get('/attendance/students/export/template', [AttendanceStudentController::class, 'exportTemplate'])
            ->name('attendance.students.export.template');
        Route::get('/attendance/students/export/excel', [AttendanceStudentController::class, 'exportExcel'])
            ->name('attendance.students.export.excel');
        Route::post('/attendance/students/bulk-action', [AttendanceStudentController::class, 'bulkAction'])
            ->name('attendance.students.bulk-action');
    });

    // 2. Admin Only: tambah, hapus, import siswa (spesifik — HARUS sebelum {student})
    Route::middleware('role:admin')->group(function () {
        Route::get('/attendance/students/import/form', [AttendanceStudentController::class, 'importForm'])
            ->name('attendance.students.import.form');
        Route::post('/attendance/students/import', [AttendanceStudentController::class, 'import'])
            ->name('attendance.students.import');
        Route::get('/attendance/students/create', [AttendanceStudentController::class, 'create'])
            ->name('attendance.students.create');
        Route::post('/attendance/students', [AttendanceStudentController::class, 'store'])
            ->name('attendance.students.store');

        // Classes Management (edit/create/delete: admin only)
        Route::get('/attendance/classes/create', [AttendanceClassController::class, 'create'])
            ->name('attendance.classes.create');
        Route::post('/attendance/classes', [AttendanceClassController::class, 'store'])
            ->name('attendance.classes.store');
    });

    // 3. Waka: index + wildcard {student} (HARUS setelah semua route spesifik di atas)
    Route::middleware('role:waka_kesiswaan')->group(function () {
        Route::get('/attendance/students', [AttendanceStudentController::class, 'index'])
            ->name('attendance.students.index');
        // Spesifik dengan {student} tapi path fixed (edit) — di atas show agar tidak konflik
        Route::get('/attendance/students/{student}/edit', [AttendanceStudentController::class, 'edit'])
            ->name('attendance.students.edit');
        Route::put('/attendance/students/{student}', [AttendanceStudentController::class, 'update'])
            ->name('attendance.students.update');
        Route::patch('/attendance/students/{student}', [AttendanceStudentController::class, 'update']);
        // Wildcard show — paling akhir di group ini
        Route::get('/attendance/students/{student}', [AttendanceStudentController::class, 'show'])
            ->name('attendance.students.show');
    });

    // 4. Admin: delete + print-qr (wildcard {student})
    Route::middleware('role:admin')->group(function () {
        Route::delete('/attendance/students/{student}', [AttendanceStudentController::class, 'destroy'])
            ->name('attendance.students.destroy');
    });

    // 5. Admin + Waka: print-qr (wildcard {student})
    Route::middleware('role:admin,waka_kesiswaan')->group(function () {
        Route::get('/attendance/students/{student}/print-qr', [StudentCardController::class, 'printSingle'])
            ->name('attendance.students.print-qr');
    });

    // Data Kelas: Waka bisa lihat
    Route::middleware('role:waka_kesiswaan')->group(function () {
        Route::get('/attendance/classes', [AttendanceClassController::class, 'index'])
            ->name('attendance.classes.index');
        Route::get('/attendance/classes/{attendanceClass}', [AttendanceClassController::class, 'show'])
            ->name('attendance.classes.show');
    });

    // Admin: Classes edit/delete (wildcard)
    Route::middleware('role:admin')->group(function () {
        Route::get('/attendance/classes/{attendanceClass}/edit', [AttendanceClassController::class, 'edit'])
            ->name('attendance.classes.edit');
        Route::put('/attendance/classes/{attendanceClass}', [AttendanceClassController::class, 'update'])
            ->name('attendance.classes.update');
        Route::patch('/attendance/classes/{attendanceClass}', [AttendanceClassController::class, 'update']);
        Route::delete('/attendance/classes/{attendanceClass}', [AttendanceClassController::class, 'destroy'])
            ->name('attendance.classes.destroy');
    });

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


    // QR Code Management — Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::post('/attendance/qr/bulk-generate', [AttendanceQRController::class, 'bulkGenerate'])
            ->name('attendance.qr.bulk-generate');
        Route::get('/attendance/qr/{student}/download-card-pdf', [AttendanceQRController::class, 'downloadCardPDF'])
            ->name('attendance.qr.download-card-pdf');
        Route::get('/attendance/qr/{student}', [AttendanceQRController::class, 'show'])
            ->name('attendance.qr.show');
        Route::get('/attendance/qr/{student}/download', [AttendanceQRController::class, 'download'])
            ->name('attendance.qr.download');
        Route::post('/attendance/qr/{student}/regenerate', [AttendanceQRController::class, 'regenerate'])
            ->name('attendance.qr.regenerate');
    });

    // Settings — Petugas hanya bisa akses halaman & simpan kamera
    Route::get('/attendance/settings', [AttendanceSettingController::class, 'index'])
        ->name('attendance.settings.index');
    Route::put('/attendance/settings', [AttendanceSettingController::class, 'update'])
        ->name('attendance.settings.update');
    Route::get('/attendance/setting-waktu', [AttendanceSettingController::class, 'settingWaktu'])
        ->name('attendance.setting-waktu.index');
    Route::put('/attendance/setting-waktu', [AttendanceSettingController::class, 'updateSettingWaktu'])
        ->name('attendance.setting-waktu.update');
    Route::get('/attendance/kamera', [AttendanceSettingController::class, 'kamera'])
        ->name('attendance.kamera.index');
    Route::put('/attendance/kamera', [AttendanceSettingController::class, 'updateKamera'])
        ->name('attendance.kamera.update');


    // Settings admin-only
    Route::middleware('role:admin')->group(function () {
        Route::post('/attendance/settings/reset', [AttendanceSettingController::class, 'reset'])
            ->name('attendance.settings.reset');
        Route::post('/attendance/settings/test-notification', [AttendanceSettingController::class, 'testNotification'])
            ->name('attendance.settings.test-notification');
        Route::get('/attendance/settings/backup', [AttendanceSettingController::class, 'downloadBackup'])
            ->name('attendance.settings.backup');
        Route::post('/attendance/settings/restore', [AttendanceSettingController::class, 'restoreBackup'])
            ->name('attendance.settings.restore');
        Route::post('/attendance/settings/send-summary', [AttendanceSettingController::class, 'sendSummaryNow'])
            ->name('attendance.settings.send-summary');
        Route::post('/attendance/settings/send-waka-summary', [AttendanceSettingController::class, 'sendWakaSummaryNow'])
            ->name('attendance.settings.send-waka-summary');
        Route::post('/attendance/settings/send-kepsek-summary', [AttendanceSettingController::class, 'sendKepsekSummaryNow'])
            ->name('attendance.settings.send-kepsek-summary');

        Route::get('/attendance/settings/photos/stats', [AttendanceSettingController::class, 'photoStats'])
            ->name('attendance.settings.photos.stats');
        Route::get('/attendance/settings/photos/download', [AttendanceSettingController::class, 'photoDownload'])
            ->name('attendance.settings.photos.download');
        Route::post('/attendance/settings/photos/cleanup', [AttendanceSettingController::class, 'photoCleanup'])
            ->name('attendance.settings.photos.cleanup');
    });

    // ==========================================
    // WhatsApp Gateway Management
    // WhatsApp Gateway — Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::get('/', [WhatsAppController::class, 'index'])->name('index');
            Route::get('/status', [WhatsAppController::class, 'status'])->name('status');
            Route::get('/health', [WhatsAppController::class, 'health'])->name('health');
            Route::get('/qr', [WhatsAppController::class, 'qrCode'])->name('qr');
            Route::get('/diagnostics', [WhatsAppController::class, 'diagnostics'])->name('diagnostics');
            Route::post('/auto-fix', [WhatsAppController::class, 'autoFix'])->name('auto-fix');
            Route::post('/diagnostic/test-send', [WhatsAppDiagnosticController::class, 'testSend'])->name('diagnostic.test-send');
            Route::get('/send', [WhatsAppController::class, 'sendPage'])->name('send');
            Route::post('/send', [WhatsAppController::class, 'send'])->name('send.submit');
            Route::post('/send-template', [WhatsAppController::class, 'sendWithTemplate'])->name('send.template');
            Route::get('/logs', [WhatsAppController::class, 'logs'])->name('logs');
            Route::get('/templates', [WhatsAppController::class, 'templates'])->name('templates');
            Route::get('/templates/create', [WhatsAppController::class, 'createTemplate'])->name('templates.create');
            Route::post('/templates', [WhatsAppController::class, 'storeTemplate'])->name('templates.store');
            Route::get('/templates/{id}/edit', [WhatsAppController::class, 'editTemplate'])->name('templates.edit');
            Route::put('/templates/{id}', [WhatsAppController::class, 'updateTemplate'])->name('templates.update');
            Route::delete('/templates/{id}', [WhatsAppController::class, 'deleteTemplate'])->name('templates.delete');
            Route::get('/settings', [WhatsAppController::class, 'settings'])->name('settings');
            Route::post('/settings', [WhatsAppController::class, 'updateSettings'])->name('settings.update');
            Route::get('/broadcast', [WhatsAppController::class, 'broadcastPage'])->name('broadcast');
            Route::post('/broadcast', [WhatsAppController::class, 'sendBroadcast'])->name('broadcast.submit');
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
    });

    // Izin Online — Petugas, Waka, Admin (bukan Kepala Sekolah)
    Route::middleware('role:petugas')->group(function () {
        Route::get('/attendance/izin', [AttendanceIzinController::class, 'adminIndex'])->name('attendance.izin.index');
        Route::post('/attendance/izin/{izin}/approve', [AttendanceIzinController::class, 'approve'])->name('attendance.izin.approve');
        Route::post('/attendance/izin/{izin}/reject', [AttendanceIzinController::class, 'reject'])->name('attendance.izin.reject');
    });

    // ==========================================
    // Manajemen User & Wali Kelas (admin only)
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/attendance/users', [WaliKelasController::class, 'userIndex'])->name('attendance.users.index');
        Route::post('/attendance/users', [WaliKelasController::class, 'userStore'])->name('attendance.users.store');
        Route::put('/attendance/users/{user}', [WaliKelasController::class, 'userUpdate'])->name('attendance.users.update');
        Route::delete('/attendance/users/{user}', [WaliKelasController::class, 'userDestroy'])->name('attendance.users.destroy');
        Route::post('/attendance/users/{user}/regenerate-code', [WaliKelasController::class, 'userRegenerateCode'])->name('attendance.users.regenerate-code');

        // Tahun Ajaran Management
        Route::get('/attendance/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('attendance.tahun-ajaran.index');
        Route::post('/attendance/tahun-ajaran', [TahunAjaranController::class, 'create'])->name('attendance.tahun-ajaran.create');
        Route::post('/attendance/tahun-ajaran/{tahunAjaran}/activate', [TahunAjaranController::class, 'activate'])->name('attendance.tahun-ajaran.activate');
        Route::post('/attendance/tahun-ajaran/preview', [TahunAjaranController::class, 'previewNaikKelas'])->name('attendance.tahun-ajaran.preview');
        Route::post('/attendance/tahun-ajaran/naik-kelas', [TahunAjaranController::class, 'naikKelas'])->name('attendance.tahun-ajaran.naik-kelas');
        Route::post('/attendance/tahun-ajaran/rollback', [TahunAjaranController::class, 'rollback'])->name('attendance.tahun-ajaran.rollback');

        // Hari Libur Management (synced from E-Kaldik)
        Route::get('/holidays', [\App\Http\Controllers\HolidayController::class, 'index'])->name('holidays.index');
        Route::post('/holidays', [\App\Http\Controllers\HolidayController::class, 'store'])->name('holidays.store');
        Route::delete('/holidays/{holiday}', [\App\Http\Controllers\HolidayController::class, 'destroy'])->name('holidays.destroy');
        Route::post('/holidays/sync', [\App\Http\Controllers\HolidayController::class, 'sync'])->name('holidays.sync');
    Route::post('/holidays/toggle-weekend', [\App\Http\Controllers\HolidayController::class, 'toggleWeekend'])->name('holidays.toggle-weekend');
    });

    // ==========================================
    // Dashboard Wali Kelas
    // ==========================================
    Route::middleware('role:wali_kelas')->group(function () {
        Route::get('/wali/dashboard', [WaliKelasController::class, 'waliDashboard'])->name('wali.dashboard');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});