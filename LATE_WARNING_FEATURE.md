# 🚨 Fitur Peringatan Keterlambatan (Late Warning)

## 📋 Deskripsi
Fitur ini mengirimkan peringatan otomatis via WhatsApp ke orang tua siswa yang **sering terlambat** dalam satu bulan. Peringatan berisi statistik lengkap keterlambatan termasuk trend.

## ✨ Cara Kerja

### Trigger
- **Real-time**: Peringatan dikirim saat siswa melakukan check-in
- **Kondisi**: Hanya dikirim jika siswa sudah memenuhi kriteria berikut:
  1. Status check-in adalah `terlambat`
  2. Keterlambatan >= threshold (default: 30 menit)
  3. Sudah terlambat >= jumlah minimal dalam bulan berjalan (default: 3x)

### Alur Proses
```
Siswa Check-In → Status: Terlambat → Kirim Notifikasi Check-In Biasa
    ↓
Cek: Terlambat >= 30 menit?
    ↓ Ya
Cek: Sudah terlambat >= 3x bulan ini?
    ↓ Ya
Hitung Statistik (Total, Menit, Trend)
    ↓
Kirim Peringatan Keterlambatan
```

## 🔧 Konfigurasi

### Database Settings
Settings disimpan di tabel `attendance_settings`:

| Key | Default | Deskripsi |
|-----|---------|-----------|
| `late_warning_enabled` | `0` | Enable/disable fitur (0=off, 1=on) |
| `late_warning_threshold_minutes` | `30` | Minimal menit terlambat untuk trigger |
| `late_warning_min_count` | `3` | Minimal keterlambatan dalam sebulan |

### UI Dashboard
Akses: **Pengaturan Sistem → ⚠️ Peringatan Keterlambatan**

Settings yang bisa dikonfigurasi:
1. **Toggle ON/OFF** - Aktifkan/nonaktifkan fitur
2. **Batas Keterlambatan** - Set berapa menit minimal terlambat (1-120 menit)
3. **Jumlah Minimal** - Set berapa kali harus terlambat dalam sebulan (1-20x)

## 📊 Isi Pesan Peringatan

Contoh pesan yang dikirim:

```
🏫 *SMK Negeri 1*
⚠️ *PERINGATAN KETERLAMBATAN*

Siswa: *Ahmad Rizki*
Kelas: X Busana

📊 *Statistik Bulan Ini:*
• Total Terlambat: *3x*
• Akumulasi Waktu: *95 menit*
• Trend: 📈 *Meningkat*

⚠️ Mohon perhatian lebih untuk kedisiplinan waktu.
Keterlambatan berulang dapat mempengaruhi prestasi belajar.

_Pesan otomatis dari sistem absensi_
```

### Trend Calculation
- **Meningkat** 📈: Keterlambatan di paruh kedua bulan >20% lebih tinggi dari paruh pertama
- **Menurun** 📉: Keterlambatan di paruh kedua bulan >20% lebih rendah dari paruh pertama
- **Stabil** ➡️: Perubahan <20%

## 🧪 Testing

### Skenario 1: Siswa Baru Terlambat (Belum Trigger)
```
Setup:
- late_warning_enabled = 1
- late_warning_threshold_minutes = 30
- late_warning_min_count = 3
- Siswa sudah terlambat 2x bulan ini

Action:
- Siswa check-in terlambat 35 menit

Expected Result:
✅ Notifikasi check-in biasa terkirim
❌ Peringatan keterlambatan TIDAK terkirim (belum 3x)
```

### Skenario 2: Siswa Sering Terlambat (Trigger)
```
Setup:
- late_warning_enabled = 1
- late_warning_threshold_minutes = 30
- late_warning_min_count = 3
- Siswa sudah terlambat 3x bulan ini (termasuk hari ini)

Action:
- Siswa check-in terlambat 35 menit

Expected Result:
✅ Notifikasi check-in biasa terkirim
✅ Peringatan keterlambatan terkirim dengan statistik lengkap
```

### Skenario 3: Terlambat Tapi Kurang dari Threshold
```
Setup:
- late_warning_enabled = 1
- late_warning_threshold_minutes = 30
- late_warning_min_count = 3
- Siswa sudah terlambat 5x bulan ini

Action:
- Siswa check-in terlambat 15 menit

Expected Result:
✅ Notifikasi check-in biasa terkirim (status: terlambat)
❌ Peringatan keterlambatan TIDAK terkirim (kurang dari 30 menit)
```

### Skenario 4: Fitur Dinonaktifkan
```
Setup:
- late_warning_enabled = 0
- Siswa sudah terlambat 5x bulan ini

Action:
- Siswa check-in terlambat 40 menit

Expected Result:
✅ Notifikasi check-in biasa terkirim
❌ Peringatan keterlambatan TIDAK terkirim (fitur off)
```

## 📁 File yang Dimodifikasi

### Backend
1. **`app/Services/AttendanceNotificationService.php`**
   - Method `notifyCheckIn()`: Tambah call ke `checkAndSendLateWarning()`
   - Method `checkAndSendLateWarning()`: Logic utama late warning
   - Method `calculateLateTrend()`: Hitung trend keterlambatan
   - Method `formatLateWarningMessage()`: Format pesan WA

2. **`app/Http/Controllers/AttendanceSettingController.php`**
   - Tambah validasi untuk 3 setting baru

3. **`database/migrations/2026_08_10_160302_add_late_warning_settings.php`**
   - Migration untuk insert 3 settings baru

### Frontend
4. **`resources/views/attendance/settings/index.blade.php`**
   - Tambah section "Peringatan Keterlambatan"
   - Toggle enable/disable
   - Input threshold minutes & min count
   - Info box & preview message
   - JavaScript `toggleLateWarningFields()`

## 🚀 Deployment ke Production

### 1. Push Code ke Git
```bash
cd /www/wwwroot/absensi
git add .
git commit -m "feat: Add late warning notification feature"
git push origin main
```

### 2. Pull di Production
```bash
ssh root@server
cd /www/wwwroot/absensi
git pull origin main
```

### 3. Run Migration
```bash
php artisan migrate
```

### 4. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 5. Aktifkan di Dashboard
1. Login ke dashboard absensi
2. Menu **Pengaturan Sistem**
3. Scroll ke **⚠️ Peringatan Keterlambatan**
4. Toggle **ON**
5. Set threshold & min count sesuai kebutuhan
6. Klik **Simpan**

## 🔍 Monitoring & Logs

Check logs untuk memastikan fitur berjalan:

```bash
tail -f storage/logs/laravel.log | grep "late_warning"
```

Log yang muncul:
- `Late warning disabled` - Fitur off
- `Student not late enough for warning` - Belum cukup terlambat
- `Student not late enough times for warning` - Belum cukup sering
- `Late warning sent` - Peringatan berhasil dikirim

## ⚠️ Catatan Penting

1. **Peringatan dikirim setiap kali check-in** jika memenuhi syarat
   - Jika siswa sudah terlambat 3x, maka check-in ke-3, ke-4, dst akan terus trigger warning
   - Pertimbangkan untuk menambah logic "max 1 warning per bulan" jika perlu

2. **Dependency: WhatsApp Gateway**
   - Pastikan gateway running di port 3001 (primary) atau 3000 (backup)
   - Test dengan command: `curl http://localhost:3001/status`

3. **Data Scope: Bulan Berjalan**
   - Perhitungan reset otomatis setiap awal bulan
   - Data history tetap tersimpan di `attendance_records`

4. **Parent Phone Required**
   - Peringatan hanya dikirim jika siswa punya `no_hp_ortu`
   - Format: 628xxx (format Indonesia)

## 🆚 Perbedaan dengan Fitur Lain

| Fitur | Trigger | Kondisi | Isi Pesan |
|-------|---------|---------|-----------|
| **Check-In Notification** | Setiap check-in | Selalu | Status + waktu masuk |
| **Late Warning** | Check-in terlambat | ≥3x/bulan & ≥30min | Statistik lengkap + trend |
| **Absent Notification** | Auto cron | Siswa alpha | Peringatan tidak hadir |

## 📝 TODO / Future Enhancement

- [ ] Add setting: Max warning per student per month (avoid spam)
- [ ] Add dashboard untuk lihat history late warning yang terkirim
- [ ] Export data siswa dengan frequent late
- [ ] Send weekly summary ke wali kelas
- [ ] Integration dengan sistem poin pelanggaran

---

**Created:** 2026-08-10  
**Version:** 1.0.0  
**Status:** ✅ Ready for Production
