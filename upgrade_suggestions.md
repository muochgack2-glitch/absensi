# 🚀 Saran Upgrade Fitur — Next Phase

## 🎨 A. UX Enhancement (Polish yang Ada)

### 1. Dashboard — Chart Interaktif + Tren
**Sekarang:** Chart bar 7 hari sederhana.
**Upgrade:** Multi-dataset chart (hadir/terlambat/alpha), tooltip detail, perbandingan minggu lalu vs sekarang.
- Effort: ⚡ Kecil | Impact: 🟡 Medium

### 2. Dashboard — Notification Center
**Sekarang:** Tidak ada notifikasi real-time.
**Upgrade:** Dropdown bell icon di navbar — tampilkan izin pending, siswa alpha hari ini, WA gagal kirim.
- Effort: 🔧 Sedang | Impact: 🔴 Tinggi

### 3. Halaman Siswa — Detail Modal / Side Panel
**Sekarang:** Klik siswa → pindah halaman baru.
**Upgrade:** Slide-over panel / modal quick view (foto, stats, riwayat 5 hari terakhir) tanpa pindah halaman.
- Effort: 🔧 Sedang | Impact: 🟡 Medium

### 4. Dark Mode Toggle di Navbar
**Sekarang:** Dark mode mengikuti sistem OS.
**Upgrade:** Tambah toggle manual (sun/moon icon) di navbar agar user bisa pilih sendiri.
- Effort: ⚡ Kecil | Impact: 🟡 Medium

---

## 🆕 B. Fitur Baru

### 5. Export Data Siswa ke Excel
**Sekarang:** Hanya bisa import, tidak bisa export.
**Upgrade:** Tombol "Export Excel" di halaman Data Siswa — export semua siswa beserta kelas dan status.
- Effort: ⚡ Kecil | Impact: 🔴 Tinggi

### 6. Rekap Absensi per Siswa (History Timeline)
**Sekarang:** Rekap hanya ada di laporan bulanan/semester.
**Upgrade:** Di halaman detail siswa, tampilkan timeline visual (calendar heatmap) — hijau hadir, merah alpha, kuning terlambat.
- Effort: 🔧 Sedang | Impact: 🔴 Tinggi

### 7. Auto-Send WA saat Siswa Alpha
**Sekarang:** WA harus kirim manual / broadcast.
**Upgrade:** Setelah jam absensi berakhir, otomatis kirim WA ke orang tua siswa yang alpha. Bisa dijadwalkan via cron job.
- Effort: 🔧 Sedang | Impact: 🔴 Tinggi

### 8. Multi-Semester Management
**Sekarang:** Tidak ada konsep semester/tahun ajaran aktif.
**Upgrade:** Tambah model `Semester` (Ganjil/Genap + Tahun Ajaran). Semua data absensi terikat ke semester aktif. Bisa switch antar semester.
- Effort: 🔧 Besar | Impact: 🔴 Tinggi

### 9. Profil Siswa — Upload Foto
**Sekarang:** Siswa pakai avatar default (inisial).
**Upgrade:** Upload foto siswa, tampilkan di kartu ID QR, detail siswa, dan portal publik.
- Effort: 🔧 Sedang | Impact: 🟡 Medium

### 10. Print Laporan Langsung dari Browser
**Sekarang:** Export PDF saja.
**Upgrade:** Tambah tombol "Print" yang langsung buka print dialog browser dengan layout yang sudah diformat (kop surat, tanda tangan).
- Effort: ⚡ Kecil | Impact: 🟡 Medium

---

## ⚙️ C. Optimasi Teknis

### 11. Caching Dashboard Stats
**Sekarang:** Setiap buka dashboard, query semua record dari DB.
**Upgrade:** Cache stats per 5 menit (Redis/file cache). Invalidate saat ada scan baru.
- Effort: ⚡ Kecil | Impact: 🟡 Medium

### 12. Rate Limiting pada Form Publik
**Sekarang:** Form izin dan portal cek kehadiran tanpa rate limit.
**Upgrade:** Tambah throttle (misal max 10 request/menit per IP) untuk cegah spam.
- Effort: ⚡ Kecil | Impact: 🟡 Medium

---

## 📋 Prioritas Rekomendasi

| Rank | Item | Effort | Impact | Rekomendasi |
|---|---|---|---|---|
| 1 | **#5 Export Siswa Excel** | ⚡ | 🔴 | Paling mudah, paling berguna |
| 2 | **#7 Auto-Send WA Alpha** | 🔧 | 🔴 | Fitur killer — hemat waktu guru |
| 3 | **#4 Dark Mode Toggle** | ⚡ | 🟡 | Quick win, banyak diminta |
| 4 | **#6 Calendar Heatmap** | 🔧 | 🔴 | Visual wow, sangat informatif |
| 5 | **#2 Notification Center** | 🔧 | 🔴 | Professional feel |
| 6 | **#10 Print Laporan** | ⚡ | 🟡 | Praktis untuk guru |
| 7 | **#12 Rate Limiting** | ⚡ | 🟡 | Security basic |
| 8 | **#8 Multi-Semester** | 🔧 | 🔴 | Penting tapi effort besar |

> [!TIP]
> Rekomendasi: Mulai dari **#5 (Export Excel)** dan **#4 (Dark Mode Toggle)** — keduanya quick win dengan impact tinggi.
