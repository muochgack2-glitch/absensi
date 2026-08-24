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
        // Get settings grouped by group_name
        $settings = AttendanceSetting::getGrouped();

        return view('attendance.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Validate all settings
        $rules = [
            'settings.check_in_time'                 => 'required|date_format:H:i',
            'settings.check_out_time'                => 'required|date_format:H:i|after:settings.check_in_time',
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
            'settings.qr_camera_index'               => 'nullable|integer|min:0|max:9',
            'settings.photo_camera_index'            => 'nullable|integer|min:0|max:9',
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

        // Simpan index kamera dari hidden input yang di-update JS
        AttendanceSetting::set('qr_camera_index',      $request->input('settings.qr_camera_index',      '0'), 'camera');
        AttendanceSetting::set('photo_camera_index',   $request->input('settings.photo_camera_index',   '1'), 'camera');
        // Simpan deviceId kamera agar welcome page bisa match by ID (tidak bergantung urutan enumerate)
        AttendanceSetting::set('qr_camera_deviceid',   $request->input('settings.qr_camera_deviceid',   ''), 'camera');
        AttendanceSetting::set('photo_camera_deviceid',$request->input('settings.photo_camera_deviceid',''), 'camera');


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

        // Handle summary send time
        if ($request->has('settings') && isset($request->settings['summary_send_time'])) {
            AttendanceSetting::set('summary_send_time', $request->settings['summary_send_time'], 'notification');
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
}
