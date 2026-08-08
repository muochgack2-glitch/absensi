# Future Plan — Sprint 3
## Sistem Absensi SMK PGRI Blora

**Status Sprint 1:** ✅ SELESAI  
**Status Sprint 2:** ✅ SELESAI  
**Status Sprint 3:** 📋 PLANNED

---

## Sprint 3 — Roadmap

### 🔴 Tier 1: Core (Paling Berdampak)

#### 1. Import Siswa via Excel
- Upload file .xlsx → parse → bulk insert ke `attendance_students`
- Template Excel bisa didownload
- Validasi: skip duplikat NIS, tampil ringkasan hasil import
- **Files:** `importStudents()`, `importTemplate()`, view import, route POST `/attendance/students/import`

#### 2. Notifikasi WA ke Ortu (Alpha/Terlambat)
- Trigger: siswa alpha atau terlambat → otomatis kirim WA ke nomor ortu
- Konten WA berbeda untuk alpha vs terlambat
- Perlu: tambah kolom `no_hp_ortu` di tabel siswa
- Setting: toggle on/off per jenis notifikasi
- **Files:** migration `no_hp_ortu`, `notifyParent()` di AttendanceService, update form siswa

#### 3. Laporan Harian PDF per Kelas
- Filter kelas + tanggal → PDF daftar hadir siap cetak + kolom TTD guru
- **Files:** `dailyPdf()` controller, view `pdf-daily.blade.php`

---

### 🟡 Tier 2: Enhancements

#### 4. Dashboard Statistik Lanjutan
- Grafik tren kehadiran 6 bulan (line chart)
- Peringkat kelas berdasarkan % kehadiran
- Top 10 siswa paling sering alpha
- **Files:** method `stats()`, view `dashboard/stats.blade.php`

#### 5. Jadwal Hari Libur
- CRUD hari libur nasional/sekolah
- Sistem skip hari libur dari perhitungan total hari sekolah
- **Files:** migration `attendance_holidays`, controller, update kalkulasi persen kehadiran

#### 6. Threshold Alert Otomatis
- Batas alpha per bulan (default 3 hari), jika lewat → WA peringatan kumulatif ke ortu
- **Files:** setting `alpha_threshold`, `checkAlphaThreshold()` di service

---

### 🟢 Tier 3: Nice to Have

#### 7. Export Surat Keterangan Absensi
- PDF resmi per siswa dengan kop surat sekolah + tanda tangan kepala sekolah

#### 8. Audit Trail Perubahan Absensi
- Log siapa mengubah status absensi, kapan, dari apa → ke apa
- Tabel `attendance_logs` + observer

---

## Rekomendasi Urutan Implementasi

| Prioritas | Fitur | Alasan |
|---|---|---|
| 1 | **Import Siswa Excel** | Setup data cepat, nilai tinggi, mudah |
| 2 | **Notif WA Ortu Alpha** | Paling diminta sekolah |
| 3 | **Laporan Harian PDF** | Kebutuhan harian guru |
| 4 | **Jadwal Hari Libur** | Memperbaiki akurasi kalkulasi |
| 5 | **Dashboard Statistik** | Insight data manajemen |
| 6 | **Threshold Alert** | Fitur lanjutan notifikasi |
