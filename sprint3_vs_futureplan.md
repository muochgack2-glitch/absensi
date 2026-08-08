# 📊 Perbandingan: Sprint 3 Plan vs FUTURE_PLAN

Dokumen ini membandingkan [sprint3_plan.md](file:///c:/Users/DMCenter/Music/SPMB2/SPMB/absensi/sprint3_plan.md) dengan [FUTURE_PLAN.md](file:///c:/Users/DMCenter/Music/SPMB2/SPMB/absensi/FUTURE_PLAN.md) untuk melihat progress dan gap.

---

## ✅ Sprint 3 Items yang SUDAH SELESAI

Berdasarkan analisis codebase, berikut item Sprint 3 yang **sudah diimplementasi**:

| # | Sprint 3 Item | Status | Bukti di Codebase |
|---|---------------|--------|-------------------|
| 1 | **Import Siswa Excel** | ✅ DONE | `AttendanceStudentController::import()`, `importForm()`, `exportTemplate()` |
| 2 | **Notif WA Ortu Alpha/Terlambat** | ✅ DONE | WhatsApp Gateway, Broadcast, Templates, Alpha Report + Notify |
| 3 | **Laporan Harian PDF** | ✅ DONE | `AttendanceReportController::exportDailyPdf()`, `pdf-daily.blade.php` |
| 4 | **Dashboard Statistik Lanjutan** | ⚠️ PARTIAL | Chart 7 hari + donut ada, tapi belum 6 bulan trend & ranking kelas |
| 5 | **Jadwal Hari Libur** | ❌ BELUM | Tidak ada model/migration `AcademicCalendar` atau `holidays` |
| 6 | **Threshold Alert Otomatis** | ⚠️ PARTIAL | Alpha Report ada, tapi belum auto-threshold per bulan |
| 7 | **Export Surat Keterangan** | ❌ BELUM | Belum ada fitur generate surat keterangan PDF |
| 8 | **Audit Trail** | ✅ DONE | `AttendanceLog` model sudah ada |

### Ringkasan Sprint 3:
- ✅ Selesai: **4 dari 8** (Import Excel, Notif WA, Laporan PDF, Audit Trail)
- ⚠️ Partial: **2 dari 8** (Dashboard Statistik, Threshold Alert)
- ❌ Belum: **2 dari 8** (Jadwal Libur, Surat Keterangan)

---

## 🔗 Pemetaan Sprint 3 → FUTURE_PLAN

| Sprint 3 Item | FUTURE_PLAN Equivalent | Coverage |
|---------------|----------------------|----------|
| 1. Import Siswa Excel | _(sudah selesai, tidak di FUTURE_PLAN)_ | ✅ Done |
| 2. Notif WA Ortu | **#2 Notifikasi Real-time** — diperluas: auto-notify check-in/out, daily summary, scheduled notification | 🔄 Extended |
| 3. Laporan Harian PDF | _(sudah selesai, tidak di FUTURE_PLAN)_ | ✅ Done |
| 4. Dashboard Statistik | **#1 Analytics Dashboard** — diperluas: heatmap, prediksi siswa berisiko, perbandingan kelas | 🔄 Extended |
| 5. Jadwal Hari Libur | **#4 Kalender Akademik** — diperluas: semester management, jadwal pelajaran, auto-skip alpha | 🔄 Extended |
| 6. Threshold Alert | **#2 Notifikasi Real-time** — termasuk alert alpha otomatis | 🔄 Included |
| 7. Surat Keterangan | **#11 Export Canggih** — termasuk custom report builder | 🔄 Included |
| 8. Audit Trail | **#12 Security & Audit** — diperluas: 2FA, session management, IP whitelist | 🔄 Extended |

---

## 🆕 Fitur BARU di FUTURE_PLAN (Tidak Ada di Sprint 3)

| # | Fitur Baru | Prioritas | Deskripsi |
|---|-----------|-----------|-----------|
| 3 | **PWA (Progressive Web App)** | 🟢 Tinggi | Install di HP, offline mode, push notification |
| 5 | **Portal Guru/Wali Kelas** | 🟡 Sedang | Dashboard per kelas, catatan perilaku, komunikasi WA per siswa |
| 6 | **Face Recognition** | 🟡 Sedang | Alternatif QR dengan deteksi wajah |
| 7 | **Integrasi API / Dapodik** | 🟡 Sedang | REST API, format Dapodik, webhook |
| 8 | **Gamifikasi Kehadiran** | 🟡 Sedang | Poin, streak, leaderboard, badge |
| 9 | **Geofencing** | 🔵 Rendah | Validasi lokasi GPS saat scan |
| 10 | **Chatbot WA Interaktif** | 🔵 Rendah | Ortu chat bot: CEK, REKAP, IZIN |

---

## 📋 Fitur yang SUDAH ADA tapi TIDAK di Sprint 3

Fitur-fitur ini sudah dibangun di luar scope Sprint 3:

| Fitur | Deskripsi |
|-------|-----------|
| **Izin Online** | Form publik ortu + approval admin (`AttendanceIzin`) |
| **Portal Ortu** | Cek absensi anak secara publik |
| **WhatsApp Gateway Ganda** | Dual gateway + failover |
| **WA Template & Broadcast** | Template pesan + broadcast massal |
| **WA Diagnostics** | Auto-fix, test send |
| **Kartu Siswa QR** | Generate & print kartu siswa |
| **Rekap Semester** | Laporan + export PDF/Excel |
| **Role Management** | Admin & Wali Kelas |
| **Bulk Actions Siswa** | Bulk delete, export, dll |
| **Backup/Restore Settings** | Download & restore konfigurasi |

---

## 🎯 Rekomendasi: Yang Harus Dikerjakan Selanjutnya

### Prioritas 1: Selesaikan Sisa Sprint 3
> Sprint 3 masih punya 2 item yang belum selesai + 2 partial

| # | Item | Effort | Impact |
|---|------|--------|--------|
| 1 | **Jadwal Hari Libur / Kalender Akademik** | Medium (3-5 hari) | Tinggi — akurasi % kehadiran |
| 2 | **Dashboard Statistik: Trend 6 bulan + Ranking Kelas** | Small (1-2 hari) | Sedang — insight manajemen |
| 3 | **Threshold Alert (auto-WA jika alpha > N)** | Small (1 hari) | Tinggi — pencegahan dini |
| 4 | **Export Surat Keterangan PDF** | Small (1-2 hari) | Sedang — kebutuhan administratif |

### Prioritas 2: Fitur Baru Paling Berdampak
| # | Item | Effort | Impact |
|---|------|--------|--------|
| 5 | **PWA** | Small (2-3 hari) | Tinggi — UX mobile |
| 6 | **Chatbot WA Interaktif** | Medium (3-5 hari) | Tinggi — engagement ortu |
| 7 | **Portal Guru diperkaya** | Medium (3-5 hari) | Sedang — operasional harian |

---

## Pertanyaan untuk User

> [!IMPORTANT]
> 1. Apakah ingin **selesaikan sisa Sprint 3 dulu** sebelum mulai fitur baru?
> 2. Atau langsung lompat ke fitur baru yang lebih menarik (PWA, Chatbot WA)?
> 3. Item Sprint 3 mana yang paling **urgent** untuk sekolah saat ini?
