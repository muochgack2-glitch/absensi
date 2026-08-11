# ✅ WhatsApp Notification Fix - COMPLETED

## Problem
- Notifikasi WhatsApp untuk check-in **TIDAK terkirim**
- Notifikasi check-out **TIDAK ADA**

## Root Cause Analysis

### Check-In Issue
1. ❌ Notifikasi hanya dikirim jika status = **'terlambat'**
2. ❌ Setting `late_notify_enabled` = **false** (dimatikan)
3. ❌ Tidak ada opsi untuk kirim notifikasi untuk **semua check-in**

### Check-Out Issue
1. ❌ Tidak ada kode notifikasi untuk check-out sama sekali
2. ❌ Tidak ada setting untuk mengaktifkan notifikasi check-out

## Solution Implemented

### 1. Check-In Notification Enhancement
**File**: `app/Services/AttendanceService.php`

**Perubahan**:
- Tambah setting baru: `notify_all_checkin` (default: **true**)
- Logika notifikasi sekarang:
  - Jika `notify_all_checkin` = **true** → kirim untuk SEMUA check-in (hadir + terlambat)
  - Jika `notify_all_checkin` = **false** → hanya kirim jika `late_notify_enabled` = true DAN status = terlambat

**Kode**:
```php
// Kirim notifikasi WA untuk check-in
$notifyAllCheckIn = AttendanceSetting::get('notify_all_checkin', 'false');
$lateNotifyEnabled = AttendanceSetting::get('late_notify_enabled', 'false');

$shouldNotify = false;

if ($notifyAllCheckIn === 'true') {
    // Kirim notifikasi untuk semua check-in (hadir dan terlambat)
    $shouldNotify = true;
} elseif ($status === 'terlambat' && $lateNotifyEnabled === 'true') {
    // Kirim notifikasi hanya untuk terlambat
    $shouldNotify = true;
}

if ($shouldNotify) {
    $record->refresh();
    try {
        $this->notificationService->notifyCheckIn($student->load('kelas'), $record);
    } catch (\Exception $e) {
        Log::warning('Check-in WA notification failed: ' . $e->getMessage());
    }
}
```

### 2. Check-Out Notification Implementation
**File**: `app/Services/AttendanceService.php`

**Perubahan**:
- Tambah notifikasi untuk check-out
- Tambah setting: `notify_checkout` (default: **true**)

**Kode**:
```php
// Kirim notifikasi WA untuk check-out (jika fitur aktif)
$notifyCheckOut = AttendanceSetting::get('notify_checkout', 'false');
if ($notifyCheckOut === 'true') {
    $record->refresh();
    try {
        $this->notificationService->notifyCheckOut($student->load('kelas'), $record);
    } catch (\Exception $e) {
        Log::warning('Check-out WA notification failed: ' . $e->getMessage());
    }
}
```

### 3. Database Settings Added

#### Migration 1: `2026_08_10_114005_add_notify_all_checkin_setting.php`
```php
DB::table('attendance_settings')->insert([
    'group_name' => 'notification',
    'key' => 'notify_all_checkin',
    'value' => 'true',
    'description' => 'Kirim notifikasi WA untuk semua check-in (hadir dan terlambat). Jika false, hanya kirim untuk terlambat.',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

#### Migration 2: `2026_08_10_114700_add_notify_checkout_setting.php`
```php
DB::table('attendance_settings')->insert([
    'group_name' => 'notification',
    'key' => 'notify_checkout',
    'value' => 'true',
    'description' => 'Kirim notifikasi WA saat siswa check-out (pulang)',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

## Settings Configuration

### Notification Settings in Database

| Setting Key | Default | Description |
|------------|---------|-------------|
| `enable_parent_notification` | `true` | Master switch untuk notifikasi orang tua |
| `notify_all_checkin` | `true` | ✅ NEW: Kirim notif untuk semua check-in |
| `late_notify_enabled` | `false` | Kirim notif hanya untuk terlambat (legacy) |
| `notify_checkout` | `true` | ✅ NEW: Kirim notif saat check-out |
| `include_photo_in_notification` | `false` | Sertakan foto dalam notifikasi |

### Notification Logic Flow

#### Check-In:
1. ✅ `enable_parent_notification` = true?
2. ✅ Siswa punya nomor HP orang tua?
3. ✅ `notify_all_checkin` = true? → **KIRIM untuk SEMUA**
4. ⚠️ ATAU (`notify_all_checkin` = false DAN `late_notify_enabled` = true DAN status = terlambat) → **KIRIM untuk TERLAMBAT**

#### Check-Out:
1. ✅ `enable_parent_notification` = true?
2. ✅ Siswa punya nomor HP orang tua?
3. ✅ `notify_checkout` = true? → **KIRIM**

## Message Format

### Check-In Message:
```
🏫 *SMK PGRI BLORA*
📍 Notifikasi Absensi

Siswa: *[Nama Siswa]*
Kelas: [Kelas]
Waktu Masuk: *[HH:mm]*
Status: ✅ Hadir / ⚠️ Terlambat

_Pesan otomatis dari sistem absensi_
```

### Check-Out Message:
```
🏫 *SMK PGRI BLORA*
📍 Notifikasi Pulang

Siswa: *[Nama Siswa]*
Kelas: [Kelas]
Waktu Pulang: *[HH:mm]*

_Pesan otomatis dari sistem absensi_
```

## Deployment Steps for Production

### 1. Pull Latest Code
```bash
cd /www/wwwroot/absensi
git pull origin main
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Verify Settings
```bash
php artisan tinker
```
Then run:
```php
echo "notify_all_checkin: " . App\Models\AttendanceSetting::get('notify_all_checkin');
echo "\nnotify_checkout: " . App\Models\AttendanceSetting::get('notify_checkout');
echo "\nenable_parent_notification: " . App\Models\AttendanceSetting::get('enable_parent_notification');
```

Expected output:
```
notify_all_checkin: true
notify_checkout: true
enable_parent_notification: 1
```

### 5. Test Notifications
1. **Test Check-In**: Scan QR code untuk check-in
2. **Check Log**: Lihat `attendance_logs` dan `whatsapp_logs` table
3. **Test Check-Out**: Scan QR code untuk check-out
4. **Verify WA**: Cek apakah notifikasi terkirim ke nomor HP orang tua

## Troubleshooting

### Notifikasi Masih Tidak Terkirim?

#### Check 1: WhatsApp Gateway Status
```bash
php artisan tinker
```
```php
$service = app(App\Services\AttendanceWhatsAppService::class);
$status = $service->getStatus();
dd($status);
```

#### Check 2: Settings
```sql
SELECT * FROM attendance_settings WHERE `key` LIKE '%notif%';
```

#### Check 3: Logs
```sql
SELECT * FROM whatsapp_logs ORDER BY created_at DESC LIMIT 10;
```

#### Check 4: Student Phone Number
```sql
SELECT nis, nama, no_hp_ortu FROM attendance_students WHERE nis = '[NIS_SISWA]';
```

### Common Issues

1. **Gateway not connected**
   - Solution: Restart WhatsApp gateway atau scan QR code ulang

2. **Phone number empty**
   - Solution: Update nomor HP orang tua di data siswa

3. **Setting disabled**
   - Solution: Aktifkan `notify_all_checkin` atau `notify_checkout` via Settings UI

4. **Master switch off**
   - Solution: Set `enable_parent_notification` = true

## Files Changed

1. ✅ `app/Services/AttendanceService.php` - Added notification logic
2. ✅ `database/migrations/2026_08_10_114005_add_notify_all_checkin_setting.php` - New migration
3. ✅ `database/migrations/2026_08_10_114700_add_notify_checkout_setting.php` - New migration

## Commit Info

**Commit**: `1ea973c - Add WhatsApp notification for check-in and check-out`

**Repository**: https://github.com/muochgack2-glitch/absensi.git

---

## Summary

✅ **Check-in notification** - Sekarang kirim untuk SEMUA check-in (tidak hanya terlambat)
✅ **Check-out notification** - BARU ditambahkan, kirim saat siswa pulang
✅ **Configurable** - Bisa diatur via settings (notify_all_checkin & notify_checkout)
✅ **Default ON** - Kedua setting default = true untuk langsung aktif

**Status**: READY TO DEPLOY & TEST 🚀
