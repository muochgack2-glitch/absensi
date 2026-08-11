# 📝 Panduan Input Manual Absensi

## Apa itu Input Manual Absensi?

**Input Manual Absensi** adalah fitur yang memungkinkan admin/guru **menandai kehadiran siswa secara manual** tanpa menggunakan QR code scan. Fitur ini berguna untuk situasi khusus atau kondisi darurat.

---

## 🎯 Fungsi Utama

### 1. Input Absensi Manual
Admin dapat langsung menandai status kehadiran siswa:
- ✅ **HADIR** - Siswa hadir tepat waktu
- ⏰ **TERLAMBAT** - Siswa terlambat
- 🏥 **IZIN** - Siswa izin (ada surat/pemberitahuan)
- 🏥 **SAKIT** - Siswa sakit
- ❌ **ALPHA** - Siswa tidak hadir tanpa keterangan
- — **SKIP** - Tidak mengubah (lewati)

### 2. Update Record yang Sudah Ada
- Jika siswa sudah scan QR, admin bisa **update statusnya**
- Misal: Siswa scan terlambat, tapi admin kasih izin → ubah jadi IZIN

### 3. Bulk Operation
- **Isi Semua:** Satu klik untuk set semua siswa dengan status yang sama
- **Batch Save:** Simpan banyak siswa sekaligus dalam 1 form

### 4. Delete Record
- Hapus record absensi jika salah input
- Bisa hapus per-siswa

---


## 💡 Kapan Menggunakan Fitur Ini?

### Skenario 1: Siswa Lupa HP / HP Rusak
```
Masalah:
- Siswa datang ke sekolah tapi lupa bawa HP
- Atau HP nya mati/rusak, tidak bisa scan QR

Solusi:
1. Admin buka Input Manual Absensi
2. Pilih tanggal hari ini + kelas siswa
3. Cari nama siswa di list
4. Klik status HADIR (H)
5. Isi jam masuk manual (misal: 07:10)
6. Simpan

Hasil:
✅ Siswa tercatat hadir meskipun tidak scan QR
✅ Ortu tetap terima notifikasi WA (jika fitur aktif)
```

### Skenario 2: Sistem QR Code Error
```
Masalah:
- QR code scanner tidak berfungsi
- Jaringan internet mati
- Server down sementara

Solusi:
1. Catat manual di kertas (sementara)
2. Setelah sistem normal
3. Input semua absensi via form ini
4. Set jam masuk sesuai catatan

Hasil:
✅ Data tetap tersimpan
✅ Laporan tetap akurat
✅ Tidak ada siswa yang kehilangan rekam kehadiran
```

### Skenario 3: Siswa Izin/Sakit (Ada Surat)
```
Masalah:
- Siswa sakit, ortu kirim surat via WA
- Atau siswa izin untuk acara keluarga

Solusi:
1. Admin buka Input Manual Absensi
2. Pilih tanggal + kelas
3. Cari nama siswa
4. Klik status SAKIT (S) atau IZIN (I)
5. Isi keterangan: "Demam tinggi" atau "Acara keluarga"
6. Simpan

Hasil:
✅ Status tercatat sebagai SAKIT/IZIN (bukan ALPHA)
✅ Rekap absensi lebih akurat
✅ Ada keterangan untuk referensi
```

### Skenario 4: Koreksi Data Salah
```
Masalah:
- Siswa A scan QR nya siswa B (salah scan)
- Status terlambat padahal seharusnya izin
- Jam masuk tercatat salah

Solusi:
1. Buka Input Manual Absensi
2. Pilih tanggal kejadian + kelas
3. Cari siswa yang salah
4. Ubah statusnya (misal dari TERLAMBAT ke IZIN)
5. Update jam masuk jika perlu
6. Atau klik tombol DELETE untuk hapus record

Hasil:
✅ Data terkoreksi
✅ Laporan jadi akurat
```


### Skenario 5: Absensi Massal untuk Event Khusus
```
Masalah:
- Event sekolah (upacara, ujian, field trip)
- Semua siswa di satu lokasi tanpa QR scanner
- Perlu catat kehadiran cepat

Solusi:
1. Guru cek kehadiran manual (panggil nama)
2. Catat di kertas: hadir, tidak hadir
3. Admin input ke sistem via form ini
4. Gunakan fitur "Isi Semua HADIR"
5. Manual ubah yang tidak hadir jadi ALPHA/IZIN

Hasil:
✅ Absensi tetap tercatat di sistem
✅ Proses cepat (bulk operation)
✅ Rekap tetap lengkap
```

### Skenario 6: Siswa Datang Sangat Terlambat (Setelah Cutoff)
```
Masalah:
- Cutoff alpha: 09:00
- Sistem otomatis marking siswa ALPHA jam 09:00
- Siswa datang jam 10:00 (setelah cutoff)
- Scan QR tetap tercatat TERLAMBAT, tapi record ALPHA sudah ada

Solusi:
1. Buka Input Manual Absensi
2. Pilih tanggal + kelas siswa
3. Cari siswa yang ALPHA tapi sudah datang
4. Ubah status dari ALPHA ke TERLAMBAT
5. Set jam masuk: 10:00
6. Isi keterangan: "Datang jam 10:00, ada keperluan"
7. Simpan

Hasil:
✅ Status berubah dari ALPHA ke TERLAMBAT
✅ Record lebih akurat
✅ Siswa tidak dihitung alpha
```

---

## 🖥️ Cara Menggunakan

### Step 1: Akses Halaman
```
Dashboard → Input Absensi Manual
atau langsung ke URL: /attendance/manual
```

### Step 2: Pilih Filter
1. **Tanggal:** Pilih tanggal yang mau diinput (default: hari ini)
2. **Kelas:** Pilih kelas siswa
3. Klik **Tampilkan**

### Step 3: Input Status Kehadiran
Untuk setiap siswa, pilih salah satu:
- **H** = Hadir (hijau)
- **T** = Terlambat (kuning)
- **I** = Izin (biru)
- **S** = Sakit (ungu)
- **A** = Alpha (merah)
- **—** = Skip / Tidak diubah (abu-abu)

### Step 4: Isi Detail (Opsional)
- **Jam Masuk:** Input jam manual (misal: 07:10)
- **Keterangan:** Tambahkan catatan (misal: "Sakit demam")

### Step 5: Simpan
Klik tombol **Simpan Absensi** di bawah tabel

---


## ⚡ Fitur Quick Actions

### 1. Isi Semua (Bulk Fill)
```
Tombol di kanan atas tabel:
- [Hadir Semua]   → Set semua siswa jadi HADIR
- [Izin Semua]    → Set semua siswa jadi IZIN
- [Sakit Semua]   → Set semua siswa jadi SAKIT
- [Alpha Semua]   → Set semua siswa jadi ALPHA

Contoh Use Case:
• Event sekolah → Klik "Hadir Semua"
• Lalu manual ubah yang tidak hadir
• Lebih cepat daripada klik satu-satu
```

### 2. Update Record Existing
```
Jika siswa sudah scan QR:
- Row nya akan highlight biru muda
- Bisa langsung ubah statusnya
- Sistem akan UPDATE (bukan INSERT baru)

Contoh:
Siswa sudah scan TERLAMBAT (via QR)
→ Admin ubah jadi IZIN (karena ada surat)
→ Klik Simpan
→ Record di-update, bukan duplikat
```

### 3. Delete Record
```
Jika ada record salah:
- Klik tombol 🗑️ (trash icon) di kolom Aksi
- Konfirmasi hapus
- Record dihapus dari database

Use Case:
- Salah input
- Duplikat data
- Testing
```

---

## 📊 Visual Guide: Status Indicator

### Status Badge Colors
```
┌─────────────────────────────────────────────────┐
│  H    T    I    S    A    —                    │
│ ┌───┬───┬───┬───┬───┬───┐                      │
│ │ H │ T │ I │ S │ A │ — │  ← Klik salah satu  │
│ └───┴───┴───┴───┴───┴───┘                      │
│  🟢  🟡  🔵  🟣  🔴  ⚪                         │
│                                                 │
│ H = Hadir       (Hijau)                        │
│ T = Terlambat   (Kuning)                       │
│ I = Izin        (Biru)                         │
│ S = Sakit       (Ungu)                         │
│ A = Alpha       (Merah)                        │
│ — = Skip        (Abu-abu, tidak diubah)        │
└─────────────────────────────────────────────────┘
```

### Table Layout
```
┌──────┬─────────────┬──────┬────────┬─────────┬──────────────┬──────┐
│ No   │ Nama Siswa  │ NIS  │ Status │ Jam     │ Keterangan   │ Aksi │
├──────┼─────────────┼──────┼────────┼─────────┼──────────────┼──────┤
│  1   │ Ahmad       │ 001  │ H T I  │ 07:10   │ -            │  🗑️  │
│      │             │      │ S A —  │         │              │      │
│      │             │      │ (klik) │         │              │      │
├──────┼─────────────┼──────┼────────┼─────────┼──────────────┼──────┤
│  2   │ Budi        │ 002  │ H T I  │ 07:30   │ Sakit demam  │  🗑️  │
│      │             │      │ S A —  │         │              │      │
│      │             │      │ (klik) │         │              │      │
└──────┴─────────────┴──────┴────────┴─────────┴──────────────┴──────┘

Status yang dipilih akan:
✅ Membesar (scale 110%)
✅ Ada ring/border tebal
✅ Opacity 100% (yang lain 40%)
```

---


## 🔄 Flow Proses: Insert vs Update

### Case 1: Insert Baru (Siswa Belum Absen)
```
Kondisi:
- Siswa belum scan QR
- Belum ada record di database untuk tanggal ini

Flow:
1. Admin pilih status HADIR (H)
2. Isi jam masuk: 07:15
3. Klik Simpan
   ↓
Database:
INSERT INTO attendance_records
  (student_id, date, status, check_in_time)
VALUES
  (123, '2026-08-11', 'hadir', '07:15:00')

Hasil:
✅ Record baru dibuat
✅ Siswa tercatat hadir
```

### Case 2: Update Existing (Siswa Sudah Absen)
```
Kondisi:
- Siswa sudah scan QR jam 07:30 → Status TERLAMBAT
- Record sudah ada di database
- Admin mau ubah jadi IZIN (karena ada surat)

Flow:
1. Row siswa highlight biru (sudah ada record)
2. Radio button default TERLAMBAT (T) sudah terpilih
3. Admin klik status IZIN (I)
4. Isi keterangan: "Surat dokter"
5. Klik Simpan
   ↓
Database:
UPDATE attendance_records
SET status = 'izin',
    notes = 'Surat dokter'
WHERE student_id = 123
  AND date = '2026-08-11'

Hasil:
✅ Record di-update (bukan duplikat)
✅ Status berubah dari TERLAMBAT → IZIN
✅ Keterangan tersimpan
```

### Case 3: Skip (Tidak Diubah)
```
Kondisi:
- Siswa sudah scan QR dengan benar
- Tidak perlu koreksi
- Admin hanya mau update siswa lain

Flow:
1. Row siswa yang sudah benar: pilih status — (SKIP)
2. Atau biarkan default SKIP (jangan klik radio lain)
3. Klik Simpan
   ↓
Logic Controller:
if ($status === 'skip') {
    continue; // Lewati, tidak insert/update
}

Hasil:
✅ Record siswa ini tidak tersentuh
✅ Data tetap sama seperti semula
✅ Hemat query database
```

---

## ⚙️ Technical Details

### Database Schema
```sql
Table: attendance_records
- id (primary key)
- student_id (foreign key → attendance_students)
- date (date)
- status (enum: hadir, terlambat, izin, sakit, alpha)
- check_in_time (time, nullable)
- check_out_time (time, nullable)
- notes (text, nullable)
- created_at
- updated_at

Unique Key: (student_id, date)
→ Satu siswa hanya bisa punya 1 record per tanggal
```

### Update or Create Logic
```php
AttendanceRecord::updateOrCreate(
    [
        'student_id' => $entry['student_id'],
        'date'       => $date,
    ],
    [
        'status'        => $entry['status'],
        'check_in_time' => $checkInTime,
        'notes'         => $entry['notes'] ?? null,
    ]
);

Artinya:
1. Cari record dengan student_id & date ini
2. Jika TIDAK ADA → INSERT baru
3. Jika SUDAH ADA → UPDATE
4. Tidak pernah duplikat ✅
```


### Default Check-In Time Logic
```php
if (empty($entry['check_in_time'])) {
    if ($status === 'hadir' || $status === 'terlambat') {
        $checkInTime = '07:00:00'; // Default jam masuk
    } else {
        $checkInTime = null; // IZIN/SAKIT/ALPHA tidak perlu jam
    }
} else {
    $checkInTime = $entry['check_in_time'];
}

Artinya:
- HADIR/TERLAMBAT tanpa jam → Default 07:00
- IZIN/SAKIT/ALPHA → Jam masuk = NULL (tidak relevan)
- Jika admin isi jam manual → Pakai yang diisi
```

---

## 🔐 Permission & Access Control

### Siapa yang Bisa Akses?
```
✅ Admin (role: admin)
✅ Guru (role: teacher)
✅ Wali Kelas (role: wali_kelas)
❌ Siswa (tidak bisa akses)
❌ Orang Tua (tidak bisa akses)

Route Protection:
Route::middleware(['auth', 'role:admin,teacher,wali_kelas'])
    ->group(function () {
        Route::get('/attendance/manual', ...);
        Route::post('/attendance/manual', ...);
        Route::delete('/attendance/manual/{record}', ...);
    });
```

### Wali Kelas Special Rule (Optional)
```php
// Jika mau restrict wali kelas hanya bisa edit kelasnya sendiri:
if (auth()->user()->role === 'wali_kelas') {
    $allowedClassId = auth()->user()->kelas_id;
    if ($classId != $allowedClassId) {
        abort(403, 'Anda hanya bisa input absensi kelas Anda sendiri');
    }
}

Note: Saat ini fitur ini belum diimplementasi
      Semua guru/admin bisa akses semua kelas
```

---

## 📱 Notifikasi WhatsApp

### Apakah Input Manual Trigger Notifikasi WA?

**TIDAK OTOMATIS!** ❌

Input manual **TIDAK** trigger notifikasi WhatsApp secara otomatis. Ini berbeda dengan scan QR code yang langsung kirim WA.

### Kenapa Tidak Auto-Send?
```
Alasan:
1. Manual input biasanya untuk koreksi/backdated
2. Ortu sudah tahu anaknya izin/sakit (ada komunikasi)
3. Avoid spam WA untuk data lama
4. Admin bisa pilih kirim manual jika perlu

Contoh:
- Siswa izin kemarin, baru input hari ini
  → Tidak perlu kirim WA lagi
- Koreksi data bulan lalu
  → Tidak relevan kirim WA sekarang
```

### Cara Kirim WA Manual (Future Feature)
```
Jika perlu implementasi:
1. Tambah checkbox "Kirim Notifikasi WA"
2. Atau tombol "Kirim WA ke Ortu" per row
3. Dispatch job untuk kirim WA

Code example:
if ($request->has('send_notification')) {
    AttendanceNotificationService::sendManual($record);
}
```

---


## 📊 Comparison: Manual Input vs QR Scan

| Aspek | QR Scan | Input Manual |
|-------|---------|--------------|
| **Method** | Siswa scan sendiri | Admin input |
| **Device** | HP siswa | PC/laptop admin |
| **Foto** | Ya (selfie siswa) | Tidak |
| **Jam** | Auto (real-time) | Manual input |
| **Notifikasi WA** | ✅ Auto-send | ❌ Tidak auto |
| **Use Case** | Daily normal | Emergency/koreksi |
| **Speed** | Cepat (1-3 detik) | Agak lama (manual) |
| **Akurasi Waktu** | Sangat akurat | Tergantung input |
| **Verifikasi** | Selfie foto | Tidak ada |
| **Bulk Operation** | Tidak bisa | ✅ Bisa (isi semua) |

---

## 🎯 Best Practices

### DO ✅
```
1. Gunakan untuk situasi darurat:
   - Siswa lupa HP
   - Sistem QR error
   - Koreksi data salah

2. Isi keterangan jika perlu:
   - "Lupa HP"
   - "Surat dokter"
   - "Acara keluarga"

3. Double check sebelum simpan:
   - Pastikan status benar
   - Jam masuk sesuai
   - Tanggal tidak salah

4. Gunakan bulk operation untuk efisiensi:
   - Event → "Hadir Semua" lalu ubah yang alpha
   - Ujian → "Hadir Semua" lalu mark absent

5. Update existing record, jangan hapus + insert baru:
   - Preserve timestamp
   - Maintain audit trail
```

### DON'T ❌
```
1. Jangan gunakan sebagai metode utama:
   - QR scan tetap lebih akurat
   - Ada foto sebagai bukti
   - Real-time notification

2. Jangan input tanpa verifikasi:
   - Cek dulu siswa benar hadir/tidak
   - Jangan asal klik "Hadir Semua"

3. Jangan lupa isi jam masuk:
   - Untuk HADIR/TERLAMBAT wajib ada jam
   - Rekap statistik butuh data jam

4. Jangan delete record sembarangan:
   - Audit trail hilang
   - Laporan jadi tidak akurat

5. Jangan input backdated tanpa alasan:
   - Data lama sebaiknya dibiarkan
   - Kecuali ada kesalahan fatal
```

---


## 🔍 Troubleshooting

### Q1: Tidak bisa simpan, ada error "Duplicate entry"
**Penyebab:** Bug di kode (seharusnya tidak terjadi karena pakai `updateOrCreate`)

**Solusi:**
```sql
-- Cek duplikat
SELECT student_id, date, COUNT(*)
FROM attendance_records
WHERE date = '2026-08-11'
GROUP BY student_id, date
HAVING COUNT(*) > 1;

-- Hapus duplikat (keep yang terbaru)
DELETE r1 FROM attendance_records r1
INNER JOIN attendance_records r2
WHERE r1.student_id = r2.student_id
  AND r1.date = r2.date
  AND r1.id < r2.id;
```

### Q2: Record tidak ter-update, malah insert baru
**Penyebab:** Unique constraint tidak ada di database

**Solusi:**
```sql
-- Tambah unique constraint
ALTER TABLE attendance_records
ADD UNIQUE KEY unique_student_date (student_id, date);
```

### Q3: Tombol "Isi Semua" tidak berfungsi
**Penyebab:** JavaScript error atau browser tidak support

**Solusi:**
1. Refresh halaman (`Ctrl+F5`)
2. Clear browser cache
3. Coba browser lain (Chrome/Firefox)
4. Cek console browser (`F12`) untuk error

### Q4: Jam masuk tidak tersimpan
**Penyebab:** Format time input tidak sesuai

**Solusi:**
- Pastikan format HH:MM (misal: 07:30)
- Jangan pakai format 12-hour (7:30 AM)
- Field jam boleh kosong (akan default 07:00)

### Q5: Status SKIP tapi tetap ter-update
**Penyebab:** Bug di controller (seharusnya `continue`)

**Cek kode:**
```php
if ($entry['status'] === 'skip') {
    $skipped++;
    continue; // ← PENTING: harus ada ini
}
```

### Q6: Setelah simpan, data tidak berubah
**Solusi:**
1. Cek apakah status SKIP (—) terpilih
2. Refresh halaman setelah simpan
3. Cek database manual:
```sql
SELECT * FROM attendance_records
WHERE student_id = 123
  AND date = '2026-08-11';
```

---

## 📈 Monitoring & Audit

### Log Manual Input
```php
// Future feature: Log siapa yang input manual
Log::info('Manual attendance input', [
    'admin_id'   => auth()->id(),
    'admin_name' => auth()->user()->name,
    'date'       => $date,
    'class_id'   => $classId,
    'records'    => $saved,
    'timestamp'  => now(),
]);

Use Case:
- Audit trail siapa yang ubah data
- Detect abuse / manipulation
- Compliance reporting
```

### Statistics
```sql
-- Berapa % absensi via manual input vs QR scan
SELECT
    'Manual' as method,
    COUNT(*) as total
FROM attendance_records
WHERE check_in_photo IS NULL -- Manual tidak ada foto

UNION ALL

SELECT
    'QR Scan' as method,
    COUNT(*) as total
FROM attendance_records
WHERE check_in_photo IS NOT NULL; -- QR scan ada foto
```

---


## 🚀 Future Enhancements

### 1. Bulk Import via Excel
```
Feature:
- Upload file Excel dengan format:
  | NIS | Nama | Status | Jam | Keterangan |
- Sistem auto-populate ke form
- Validasi data sebelum simpan

Use Case:
- Input absensi dari catatan kertas
- Migration data lama
- Batch correction
```

### 2. Send Notification Option
```
Feature:
- Checkbox "Kirim Notifikasi WA" per row
- Atau tombol "Kirim WA" setelah simpan

Flow:
1. Admin input absensi manual
2. Centang "Kirim Notifikasi"
3. Simpan
4. Sistem kirim WA ke ortu

Use Case:
- Input izin/sakit yang baru diketahui
- Koreksi status yang perlu dikomunikasikan
```

### 3. Mobile-Friendly Interface
```
Feature:
- Responsive design untuk tablet/HP
- Swipe gesture untuk ganti status
- Quick filter & search

Use Case:
- Guru input dari HP saat di kelas
- Wali kelas input saat home visit
```

### 4. History & Audit Trail
```
Feature:
- Log semua perubahan manual
- Siapa yang edit, kapan, dari apa ke apa

Table: attendance_audit_logs
- id
- record_id
- user_id
- old_status
- new_status
- old_time
- new_time
- reason (keterangan)
- created_at

Use Case:
- Compliance audit
- Detect manipulation
- Admin review
```

### 5. Approval Workflow
```
Feature:
- Guru input → Status "Pending"
- Admin review → Approve/Reject
- Notifikasi approval ke guru

Use Case:
- Sekolah dengan policy ketat
- Multi-level verification
- Prevent fraud
```

### 6. Template / Preset
```
Feature:
- Save template untuk event recurring
- Misal: "Ujian Nasional - Semua Hadir"
- Load template 1-click

Use Case:
- Event rutin (upacara, ujian)
- Hemat waktu input
- Konsistensi data
```

---

## 📚 Related Documentation

- **Scan QR Code:** `PANDUAN_SCAN_QR.md` (untuk siswa)
- **Settings:** `PANDUAN_FITUR_SETTINGS.md` (notifikasi, waktu)
- **Dashboard:** `PANDUAN_DASHBOARD.md` (monitoring)
- **Laporan:** `PANDUAN_LAPORAN.md` (export, statistik)

---

## 🔗 Quick Links

### Routes
```
GET  /attendance/manual              → Tampilkan form
POST /attendance/manual              → Simpan absensi
DELETE /attendance/manual/{record}   → Hapus record
```

### Files
```
Controller: app/Http/Controllers/AttendanceManualController.php
View:       resources/views/attendance/manual/index.blade.php
Model:      app/Models/AttendanceRecord.php
            app/Models/AttendanceStudent.php
            app/Models/AttendanceClass.php
```

---

## ✅ Summary

**Input Manual Absensi** adalah fitur **backup/emergency** untuk menandai kehadiran siswa tanpa QR scan. 

**Kegunaan Utama:**
- ✅ Siswa lupa HP / HP rusak
- ✅ Sistem QR error
- ✅ Input izin/sakit dengan surat
- ✅ Koreksi data salah
- ✅ Absensi event khusus

**Key Features:**
- 📝 Input manual 6 status (Hadir, Terlambat, Izin, Sakit, Alpha, Skip)
- ⚡ Bulk operation (Isi Semua)
- 🔄 Update/Insert otomatis (tidak duplikat)
- 🗑️ Delete record
- ⏰ Custom jam masuk
- 📝 Field keterangan

**Perbedaan dengan QR Scan:**
- ❌ Tidak ada foto
- ❌ Tidak auto-send WA
- ✅ Bisa bulk operation
- ✅ Bisa backdated
- ✅ Bisa update existing

**Remember:** QR scan tetap metode utama. Manual input hanya untuk situasi khusus! 🎯

---

**Dokumen dibuat:** 11 Agustus 2026  
**Versi:** 1.0  
**Last Updated:** Commit 56c4534
