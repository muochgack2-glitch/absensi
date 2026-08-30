<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class AttendanceSettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = AttendanceSetting::getGrouped();
        return view('attendance.settings.index', compact('settings'));
    }

    /**
     * Setting Waktu page
     */
    public function settingWaktu()
    {
        $settings = AttendanceSetting::getGrouped();
        return view('attendance.settings.waktu', compact('settings'));
    }

    public function updateSettingWaktu(Request $request)
    {
        $keys = [
            'check_in_time'      => ['group' => 'time'],
            'check_out_time'     => ['group' => 'time'],
            'check_out_start_time' => ['group' => 'time'],
            'cutoff_time'        => ['group' => 'time'],
            'tolerance_minutes'  => ['group' => 'tolerance'],
            'modal_auto_close'   => ['group' => 'general'],
        ];

        foreach ($keys as $key => $meta) {
            if (isset($request->settings[$key])) {
                AttendanceSetting::set($key, $request->settings[$key], $meta['group']);
            }
        }

        AttendanceSetting::clearCache();

        return redirect()
            ->route('attendance.setting-waktu.index')
            ->with('success', 'Setting waktu berhasil disimpan.');
    }

    /**
     * Setting Kamera page
     */
    public function kamera()
    {
        $settings = AttendanceSetting::getGrouped();
        return view('attendance.settings.kamera', compact('settings'));
    }

    public function updateKamera(Request $request)
    {
        // use_dual_camera (checkbox)
        AttendanceSetting::set('use_dual_camera',
            $request->input('settings.use_dual_camera', '0') == '1' ? '1' : '0', 'camera');

        $camKeys = ['scan_fps', 'scan_resolution_qr', 'scan_resolution_photo', 'qr_camera_index', 'photo_camera_index'];
        foreach ($camKeys as $key) {
            if (isset($request->settings[$key])) {
                AttendanceSetting::set($key, $request->settings[$key], 'camera');
            }
        }

        AttendanceSetting::clearCache();

        return redirect()
            ->route('attendance.kamera.index')
            ->with('success', 'Setting kamera berhasil disimpan.');
    }

    /**
     * Setting Notifikasi page
     */
    public function notifikasi()
    {
        $settings = AttendanceSetting::getGrouped();
        return view('attendance.settings.notifikasi', compact('settings'));
    }

    public function updateNotifikasi(Request $request)
    {
        // Checkbox toggles
        foreach (['enable_parent_notification', 'include_photo_in_notification', 'auto_absent_notify'] as $key) {
            AttendanceSetting::set($key,
                $request->input("settings.{$key}", '0') == '1' ? '1' : '0', 'notification');
        }
        // late_notify_enabled pakai 'true'/'false'
        AttendanceSetting::set('late_notify_enabled',
            $request->input('settings.late_notify_enabled') === 'true' ? 'true' : 'false', 'notification');

        // Waktu & hari alpha
        if ($request->has('settings.absent_notify_time')) {
            AttendanceSetting::set('absent_notify_time', $request->settings['absent_notify_time'], 'notification');
        }
        // Hari: dikumpulkan dari array absent_days[]
        $days = collect($request->input('absent_days', []))->filter()->implode(',');
        AttendanceSetting::set('absent_notify_days', $days ?: '', 'notification');

        AttendanceSetting::clearCache();
        return redirect()->route('attendance.notifikasi.index')->with('success', 'Setting notifikasi berhasil disimpan.');
    }

    /**
     * Setting Ringkasan page
     */
    public function ringkasan()
    {
        $settings = AttendanceSetting::getGrouped();
        return view('attendance.settings.ringkasan', compact('settings'));
    }

    public function updateRingkasan(Request $request)
    {
        // Toggles
        foreach (['summary_wali_kelas_enabled', 'waka_summary_enabled', 'kepsek_summary_enabled'] as $key) {
            AttendanceSetting::set($key,
                $request->input("settings.{$key}", '0') == '1' ? '1' : '0', 'notification');
        }

        // Jam & hari wali kelas
        $timeKeys = ['summary_send_time', 'summary_pulang_send_time', 'waka_summary_masuk_time', 'waka_summary_pulang_time', 'kepsek_summary_time', 'kepsek_summary_pulang_time'];
        foreach ($timeKeys as $key) {
            if (isset($request->settings[$key])) {
                AttendanceSetting::set($key, $request->settings[$key], 'notification');
            }
        }

        // Hari-hari (dikumpulkan dari checkbox[] sebelum submit via JS ke hidden input)
        foreach (['summary_send_days' => 'summary_days', 'waka_summary_send_days' => 'waka_days', 'kepsek_summary_send_days' => 'kepsek_days'] as $settingKey => $inputName) {
            $days = collect($request->input($inputName, []))->filter()->implode(',');
            AttendanceSetting::set($settingKey, $days ?: '', 'notification');
        }

        AttendanceSetting::clearCache();
        return redirect()->route('attendance.ringkasan.index')->with('success', 'Setting ringkasan berhasil disimpan.');
    }

    /**
     * Update settings
     */

    public function update(Request $request)

    {
        // Petugas: hanya boleh simpan setting kamera
        if (auth()->user()?->isPetugas()) {
            $dualCam = $request->input('settings.use_dual_camera', '0');
            AttendanceSetting::set('use_dual_camera', $dualCam === '1' ? '1' : '0', 'camera');

            $scanFps = (int) $request->input('settings.scan_fps', 10);
            AttendanceSetting::set('scan_fps', max(5, min(30, $scanFps)), 'camera');

            $scanResQr = $request->input('settings.scan_resolution_qr', 'hd');
            if (!in_array($scanResQr, ['sd', 'hd', 'fhd'])) $scanResQr = 'hd';
            AttendanceSetting::set('scan_resolution_qr', $scanResQr, 'camera');

            $scanResPhoto = $request->input('settings.scan_resolution_photo', 'hd');
            if (!in_array($scanResPhoto, ['sd', 'hd', 'fhd'])) $scanResPhoto = 'hd';
            AttendanceSetting::set('scan_resolution_photo', $scanResPhoto, 'camera');

            return back()->with('success', 'Pengaturan kamera berhasil disimpan.');
        }

        // Validate all settings
        $rules = [
            'settings.check_in_time'                 => 'required|date_format:H:i',
            'settings.check_out_time'                => 'required|date_format:H:i|after:settings.check_in_time',
            'settings.check_out_start_time'          => 'nullable|date_format:H:i',
            'settings.tolerance_minutes'             => 'required|integer|min:0|max:60',
            'settings.cutoff_time'                   => 'required|date_format:H:i|after:settings.check_in_time',
            'settings.enable_parent_notification'    => 'nullable|boolean',
            'settings.include_photo_in_notification' => 'nullable|boolean',
            'settings.school_name'                   => 'required|string|max:100',
            'settings.announcement'                  => 'nullable|string|max:255',
            'settings.auto_absent_notify'            => 'nullable|boolean',
            'settings.absent_notify_time'            => 'nullable|date_format:H:i',
            'settings.absent_notify_days'            => 'nullable|string',
            'settings.late_notify_enabled'           => 'nullable|string|in:true,false',
            'settings.late_warning_enabled'          => 'nullable|in:0,1',
            'settings.late_warning_threshold_minutes' => 'nullable|integer|min:1|max:120',
            'settings.late_warning_min_count'        => 'nullable|integer|min:1|max:20',
            'settings.use_dual_camera'               => 'nullable|in:0,1',
            'settings.modal_auto_close'              => 'nullable|integer|min:1|max:10',
        ];

        $messages = [
            'settings.check_in_time.required' => 'Jam masuk wajib diisi.',
            'settings.check_in_time.date_format' => 'Format jam masuk tidak valid (HH:MM).',
            'settings.check_out_time.required' => 'Jam pulang wajib diisi.',
            'settings.check_out_time.date_format' => 'Format jam pulang tidak valid (HH:MM).',
            'settings.check_out_time.after' => 'Jam pulang harus setelah jam masuk.',
            'settings.tolerance_minutes.required' => 'Toleransi keterlambatan wajib diisi.',
            'settings.tolerance_minutes.integer' => 'Toleransi keterlambatan harus berupa angka.',
            'settings.tolerance_minutes.min' => 'Toleransi minimal 0 menit.',
            'settings.tolerance_minutes.max' => 'Toleransi maksimal 60 menit.',
            'settings.cutoff_time.required' => 'Batas waktu alpha wajib diisi.',
            'settings.cutoff_time.date_format' => 'Format batas waktu tidak valid (HH:MM).',
            'settings.cutoff_time.after' => 'Batas waktu harus setelah jam masuk.',
            'settings.school_name.required' => 'Nama sekolah wajib diisi.',
            'settings.school_name.max' => 'Nama sekolah maksimal 100 karakter.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update each setting
        $validated = $validator->validated();
        $settingsData = $validated['settings'];

        foreach ($settingsData as $key => $value) {
            AttendanceSetting::set($key, $value);
        }

        // Handle late_warning_enabled checkbox (jika unchecked, tidak ada di request)
        if (!isset($request->settings['late_warning_enabled'])) {
            AttendanceSetting::set('late_warning_enabled', '0');
        }

        // Handle late_notify_enabled checkbox (jika unchecked, tidak ada di request)
        if (!isset($request->settings['late_notify_enabled'])) {
            AttendanceSetting::set('late_notify_enabled', 'false');
        }

        // Handle auto_absent_notify checkbox
        if (!isset($request->settings['auto_absent_notify'])) {
            AttendanceSetting::set('auto_absent_notify', '0');
        }

        // Handle use_dual_camera checkbox
        // Pakai nilai langsung (bukan isset) karena tanpa hidden, '0' jika unchecked
        $dualCam = $request->input('settings.use_dual_camera', '0');
        AttendanceSetting::set('use_dual_camera', $dualCam === '1' ? '1' : '0', 'camera');

        // — masing-masing browser/PC menyimpan deviceId di localStorage sendiri

        // Simpan agresivitas scan (FPS) — pastikan dalam range 5–30
        $scanFps = (int) $request->input('settings.scan_fps', 10);
        $scanFps = max(5, min(30, $scanFps));
        AttendanceSetting::set('scan_fps', $scanFps, 'camera');

        // Simpan jam mulai scanner pulang dengan group 'time'
        if ($request->has('settings') && isset($request->settings['check_out_start_time'])) {
            AttendanceSetting::set('check_out_start_time', $request->settings['check_out_start_time'], 'time');
        }

        // Simpan resolusi kamera QR
        $scanResQr = $request->input('settings.scan_resolution_qr', 'hd');
        if (!in_array($scanResQr, ['sd', 'hd', 'fhd'])) $scanResQr = 'hd';
        AttendanceSetting::set('scan_resolution_qr', $scanResQr, 'camera');

        // Simpan resolusi kamera Foto (dual camera)
        $scanResPhoto = $request->input('settings.scan_resolution_photo', 'hd');
        if (!in_array($scanResPhoto, ['sd', 'hd', 'fhd'])) $scanResPhoto = 'hd';
        AttendanceSetting::set('scan_resolution_photo', $scanResPhoto, 'camera');


        // Handle absent notify days (dari checkbox terpisah, sudah dikumpulkan JS)
        // Jika kosong (semua dicentang batal), simpan string kosong
        if ($request->has('settings') && isset($request->settings['absent_notify_days'])) {
            $days = $request->settings['absent_notify_days'] ?? '';
            AttendanceSetting::set('absent_notify_days', $days);
        }

        // Handle summary_wali_kelas_enabled checkbox
        if (!isset($request->settings['summary_wali_kelas_enabled'])) {
            AttendanceSetting::set('summary_wali_kelas_enabled', '0', 'notification');
        } else {
            AttendanceSetting::set('summary_wali_kelas_enabled', '1', 'notification');
        }

        // Handle summary send days
        if ($request->has('settings') && isset($request->settings['summary_send_days'])) {
            $summaryDays = $request->settings['summary_send_days'] ?? '';
            AttendanceSetting::set('summary_send_days', $summaryDays, 'notification');
        }

        // Handle summary send time (masuk)
        if ($request->has('settings') && isset($request->settings['summary_send_time'])) {
            AttendanceSetting::set('summary_send_time', $request->settings['summary_send_time'], 'notification');
        }

        // Handle summary pulang send time
        if ($request->has('settings') && isset($request->settings['summary_pulang_send_time'])) {
            AttendanceSetting::set('summary_pulang_send_time', $request->settings['summary_pulang_send_time'], 'notification');
        }

        // ── Waka Kesiswaan ──────────────────────────────────────────────────────
        AttendanceSetting::set('waka_summary_enabled',
            isset($request->settings['waka_summary_enabled']) ? '1' : '0', 'notification');

        if (isset($request->settings['waka_summary_masuk_time'])) {
            AttendanceSetting::set('waka_summary_masuk_time', $request->settings['waka_summary_masuk_time'], 'notification');
        }
        if (isset($request->settings['waka_summary_pulang_time'])) {
            AttendanceSetting::set('waka_summary_pulang_time', $request->settings['waka_summary_pulang_time'], 'notification');
        }
        if (isset($request->settings['waka_summary_send_days'])) {
            AttendanceSetting::set('waka_summary_send_days', $request->settings['waka_summary_send_days'], 'notification');
        }

        // ── Kepala Sekolah ───────────────────────────────────────────────────────
        AttendanceSetting::set('kepsek_summary_enabled',
            isset($request->settings['kepsek_summary_enabled']) ? '1' : '0', 'notification');

        if (isset($request->settings['kepsek_summary_time'])) {
            AttendanceSetting::set('kepsek_summary_time', $request->settings['kepsek_summary_time'], 'notification');
        }
        if (isset($request->settings['kepsek_summary_pulang_time'])) {
            AttendanceSetting::set('kepsek_summary_pulang_time', $request->settings['kepsek_summary_pulang_time'], 'notification');
        }
        if (isset($request->settings['kepsek_summary_send_days'])) {
            AttendanceSetting::set('kepsek_summary_send_days', $request->settings['kepsek_summary_send_days'], 'notification');
        }


        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            $logo = $request->file('school_logo');
            $logoPath = $logo->store('settings', 'public');
            AttendanceSetting::set('school_logo', $logoPath);
        }

        // Handle school address — default '' agar tidak NULL di DB
        $schoolAddress = $request->input('school_address', '');
        AttendanceSetting::set('school_address', $schoolAddress ?? '');


        // Clear settings cache
        AttendanceSetting::clearCache();

        return redirect()
            ->route('attendance.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        // Default settings
        $defaults = [
            'check_in_time'                  => '07:00',
            'check_out_time'                 => '15:00',
            'tolerance_minutes'              => '15',
            'cutoff_time'                    => '09:00',
            'enable_parent_notification'     => '1',
            'include_photo_in_notification'  => '1',
            'school_name'                    => 'SMK Negeri 1',
            'auto_absent_notify'             => '0',
            'absent_notify_time'             => '09:00',
            'absent_notify_days'             => '1,2,3,4,5',
        ];

        foreach ($defaults as $key => $value) {
            AttendanceSetting::set($key, $value);
        }

        // Clear settings cache
        AttendanceSetting::clearCache();

        return redirect()
            ->route('attendance.settings.index')
            ->with('success', 'Pengaturan berhasil direset ke default.');
    }

    /**
     * Get settings as JSON (for API)
     */
    public function getSettings(Request $request)
    {
        $group = $request->input('group');

        if ($group) {
            $settings = AttendanceSetting::getByGroup($group);
        } else {
            $settings = AttendanceSetting::getGrouped();
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update single setting (for API)
     */
    public function updateSetting(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $setting = AttendanceSetting::where('key', $validated['key'])->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan tidak ditemukan.'
            ], 404);
        }

        AttendanceSetting::set($validated['key'], $validated['value']);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil diperbarui.',
            'data' => [
                'key' => $validated['key'],
                'value' => $validated['value']
            ]
        ]);
    }

    /**
     * Test notification settings
     */
    public function testNotification(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|regex:/^628[0-9]{9,12}$/',
        ]);

        // Send test notification
        $whatsappService = app(\App\Services\AttendanceWhatsAppService::class);

        $message = "🔔 *Test Notifikasi*\n\n";
        $message .= "Ini adalah pesan test dari Sistem Absensi.\n";
        $message .= "Jika Anda menerima pesan ini, artinya konfigurasi WhatsApp Gateway sudah benar.\n\n";
        $message .= "_" . AttendanceSetting::get('school_name', 'SMK Negeri 1') . "_";

        $result = $whatsappService->sendParentNotification(
            $validated['phone'],
            $message
        );

        if ($result['success']) {
            return back()->with('success', 'Notifikasi test berhasil dikirim ke ' . $validated['phone']);
        } else {
            return back()->withErrors(['notification' => 'Gagal mengirim notifikasi: ' . $result['message']]);
        }
    }

    /**
     * Download database backup as SQL file (via PHP PDO — no mysqldump needed)
     */
    public function downloadBackup()
    {
        $filename = 'backup_absensi_' . now()->format('Ymd_His') . '.sql';
        $db = config('database.connections.' . config('database.default'));

        $pdo = new \PDO(
            "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
            $db['username'],
            $db['password']
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql  = "-- Backup Database: {$db['database']}\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- System: Sistem Absensi QR — " . AttendanceSetting::get('school_name', 'Sekolah') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Ambil semua tabel
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // CREATE TABLE
            $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createRow['Create Table'] . ";\n\n";

            // INSERT DATA
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $cols = '`' . implode('`, `', array_keys($rows[0])) . '`';
                $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $escaped = array_map(fn($v) => is_null($v) ? 'NULL' : $pdo->quote($v), $row);
                    $values[] = '(' . implode(', ', $escaped) . ')';
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return response($sql, 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($sql),
        ]);
    }

    /**
     * Restore database dari file SQL yang diupload.
     * ⚠️ BERBAHAYA: akan DROP dan recreate semua tabel dalam file SQL.
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:51200', // max 50MB
        ], [
            'sql_file.required' => 'Pilih file SQL backup terlebih dahulu.',
            'sql_file.mimes'    => 'File harus berekstensi .sql atau .txt',
            'sql_file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $file    = $request->file('sql_file');
        $sqlContent = file_get_contents($file->getRealPath());

        if (empty(trim($sqlContent))) {
            return back()->withErrors(['sql_file' => 'File SQL kosong.']);
        }

        // Validasi sederhana: harus ada kata kunci SQL
        if (!str_contains($sqlContent, 'CREATE TABLE') && !str_contains($sqlContent, 'INSERT INTO')) {
            return back()->withErrors(['sql_file' => 'File bukan SQL backup yang valid.']);
        }

        $db = config('database.connections.' . config('database.default'));

        try {
            $pdo = new \PDO(
                "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password']
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Nonaktifkan foreign key check selama restore
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
            $pdo->exec('SET sql_mode="";');

            // Pisahkan statement SQL berdasarkan ";"
            // Hapus komentar SQL (-- ...) dan baris kosong
            $lines = explode("\n", $sqlContent);
            $cleanLines = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (str_starts_with($trimmed, '--') || $trimmed === '') continue;
                $cleanLines[] = $trimmed;
            }
            $cleanSql = implode("\n", $cleanLines);

            // Split per statement
            $statements = array_filter(
                array_map('trim', explode(';', $cleanSql)),
                fn($s) => !empty($s)
            );

            $executed = 0;
            foreach ($statements as $stmt) {
                if (!empty(trim($stmt))) {
                    $pdo->exec($stmt);
                    $executed++;
                }
            }

            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            // Clear semua cache settings setelah restore
            AttendanceSetting::clearCache();

            return back()->with('success', "✅ Restore berhasil! {$executed} statement SQL dieksekusi. Silakan refresh halaman.");

        } catch (\Exception $e) {
            return back()->withErrors([
                'sql_file' => '❌ Restore gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Kirim ringkasan kehadiran ke wali kelas sekarang (manual trigger)
     */
    public function sendSummaryNow(): \Illuminate\Http\JsonResponse
    {
        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('attendance:send-summary');
            $output   = \Illuminate\Support\Facades\Artisan::output();

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0
                    ? 'Ringkasan berhasil dikirim ke wali kelas!'
                    : 'Ada masalah saat pengiriman.',
                'output'  => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendWakaSummaryNow(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $type = in_array($request->input('type'), ['masuk', 'pulang']) ? $request->input('type') : 'masuk';
        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('attendance:send-waka-summary', ['--type' => $type]);
            $output   = \Illuminate\Support\Facades\Artisan::output();
            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0
                    ? 'Ringkasan ' . ($type === 'pulang' ? 'pulang' : 'masuk') . ' berhasil dikirim ke Waka Kesiswaan!'
                    : 'Ada masalah saat pengiriman.',
                'output'  => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function sendKepsekSummaryNow(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $type = in_array($request->input('type'), ['masuk', 'pulang']) ? $request->input('type') : 'masuk';
        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('attendance:send-kepsek-summary', ['--type' => $type]);
            $output   = \Illuminate\Support\Facades\Artisan::output();
            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0
                    ? 'Laporan ' . ($type === 'pulang' ? 'pulang' : 'masuk') . ' berhasil dikirim ke Kepala Sekolah!'
                    : 'Ada masalah saat pengiriman.',
                'output'  => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get photo storage stats (JSON)
     */
    public function photoStats()
    {
        $basePath = storage_path('app/public/attendance/photos');
        $totalFiles = 0;
        $totalBytes = 0;
        $oldest = null;
        $newest = null;

        if (is_dir($basePath)) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','webp'])) {
                    $totalFiles++;
                    $totalBytes += $file->getSize();
                    // Ambil tanggal dari nama folder (format Y-m-d)
                    $parts = explode(DIRECTORY_SEPARATOR, $file->getPath());
                    $datePart = end($parts);
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePart)) {
                        if ($oldest === null || $datePart < $oldest) $oldest = $datePart;
                        if ($newest === null || $datePart > $newest) $newest = $datePart;
                    }
                }
            }
        }

        return response()->json([
            'total_files' => $totalFiles,
            'total_mb'    => round($totalBytes / 1024 / 1024, 2),
            'oldest_date' => $oldest,
            'newest_date' => $newest,
        ]);
    }

    /**
     * Download all photos as ZIP
     */
    public function photoDownload(\Illuminate\Http\Request $request)
    {
        $days    = (int) $request->get('days', 0); // 0 = semua
        $basePath = storage_path('app/public/attendance/photos');
        $zipName  = 'foto_absensi_' . now()->format('Y-m-d') . ($days ? "_older{$days}days" : '_all') . '.zip';
        $tmpZip   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zip = new \ZipArchive();
        if ($zip->open($tmpZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP');
        }

        $cutoff = $days > 0 ? \Carbon\Carbon::now()->subDays($days)->toDateString() : null;

        if (is_dir($basePath)) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if (!$file->isFile()) continue;
                // Cek cutoff
                if ($cutoff) {
                    $parts    = explode(DIRECTORY_SEPARATOR, $file->getPath());
                    $datePart = end($parts);
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datePart) && $datePart >= $cutoff) continue;
                }
                // Relative path di dalam ZIP: NIS/tanggal/filename
                $relative = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $zip->addFile($file->getPathname(), $relative);
            }
        }

        $zip->close();

        return response()->download($tmpZip, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Manual cleanup old photos
     */
    public function photoCleanup(\Illuminate\Http\Request $request)
    {
        $days = (int) $request->get('days', 30);
        if ($days < 1 || $days > 3650) {
            return response()->json(['success' => false, 'message' => 'Hari tidak valid'], 422);
        }

        try {
            $result = \Artisan::call('attendance:cleanup-photos', ['--days' => $days]);
            $output = \Artisan::output();

            // Ambil angka dari output
            preg_match('/(\d+) foto \(([0-9.]+) MB\)/', $output, $m);
            $deleted = $m[1] ?? 0;
            $mb      = $m[2] ?? 0;

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} foto ({$mb} MB)",
                'deleted' => (int) $deleted,
                'mb'      => (float) $mb,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
