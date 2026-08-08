# 🚀 Rencana Fitur Lanjutan — Sistem Absensi

Berikut adalah daftar fitur lanjutan yang disarankan berdasarkan analisis codebase yang sudah ada. Fitur-fitur dikelompokkan berdasarkan **prioritas** dan **kompleksitas**.

---

## Fitur yang Sudah Ada (Recap)

| Modul | Fitur |
|-------|-------|
| **Absensi** | QR Scanner, Manual Input, Check-in/Check-out dengan foto |
| **Dashboard** | Statistik harian, chart 7 hari, donut status, filter kelas |
| **Laporan** | Harian, Bulanan, Semester, Alpha Report, Export PDF & Excel |
| **Siswa** | CRUD, Import/Export Excel, Bulk Action, Kartu Siswa QR |
| **Kelas** | CRUD kelas, tingkat |
| **Izin Online** | Form publik ortu, approval admin, lampiran |
| **WhatsApp** | Gateway, Dual Gateway + Failover, Templates, Broadcast, Logs, Diagnostics |
| **Portal Ortu** | Cek absensi anak secara publik |
| **User Mgmt** | Admin & Wali Kelas, Role-based access |
| **Settings** | Jam masuk/pulang, notifikasi, backup/restore |

---

## 🟢 Prioritas Tinggi (High Impact, Rekomendasi Utama)

### 1. 📊 Dashboard Analytics Lanjutan
> Tingkatkan insight dari data absensi yang sudah terkumpul

- **Tren Kehadiran Mingguan/Bulanan** — grafik line interaktif per kelas
- **Heatmap Kehadiran** — visualisasi calendar heatmap (seperti GitHub contribution)
- **Top 10 Siswa Alpha** — leaderboard siswa dengan ketidakhadiran tertinggi
- **Prediksi Siswa Berisiko** — alert otomatis siswa yang pattern-nya menurun
- **Perbandingan Antar Kelas** — ranking kelas berdasarkan persentase kehadiran

#### Proposed Changes
- `[NEW] app/Http/Controllers/AnalyticsDashboardController.php`
- `[NEW] resources/views/attendance/analytics/` — halaman analytics
- `[MODIFY] routes/web.php` — tambah route analytics

---

### 2. 🔔 Sistem Notifikasi Real-time
> Notifikasi otomatis ke admin, wali kelas, dan ortu

- **Auto-notify ortu** saat anak check-in/check-out (via WhatsApp yang sudah ada)
- **Alert alpha otomatis** — kirim WA ke ortu jika anak tidak hadir sampai jam tertentu
- **Notifikasi terlambat** — info ke wali kelas saat siswa terlambat
- **Ringkasan harian** — kirim rekap otomatis ke wali kelas setiap sore
- **Scheduled notification** — gunakan Laravel Queue + Scheduler

#### Proposed Changes
- `[NEW] app/Jobs/SendAttendanceNotificationJob.php`
- `[NEW] app/Jobs/DailySummaryJob.php`
- `[NEW] app/Listeners/AttendanceRecordedListener.php`
- `[MODIFY] app/Console/Kernel.php` — schedule daily summary
- `[MODIFY] app/Services/AttendanceService.php` — dispatch event setelah scan

---

### 3. 📱 Progressive Web App (PWA)
> Jadikan aplikasi bisa di-install di HP tanpa Play Store

- **Manifest.json** + Service Worker
- **Offline mode** — cache scanner page agar bisa buka tanpa internet stabil
- **Push Notification** — notif di HP admin/wali kelas
- **Add to Home Screen** — siswa/ortu bisa pasang di HP

#### Proposed Changes
- `[NEW] public/manifest.json`
- `[NEW] public/sw.js`
- `[MODIFY] resources/views/layouts/` — tambah meta PWA

---

### 4. 📅 Manajemen Jadwal & Kalender Akademik
> Atur hari libur, ujian, acara sekolah

- **Kalender Akademik** — tandai libur nasional, libur sekolah, ujian
- **Auto-skip absensi** di hari libur (tidak hitung alpha)
- **Jadwal pelajaran per kelas** — absensi per mata pelajaran (opsional)
- **Semester management** — set tanggal awal/akhir semester

#### Proposed Changes
- `[NEW] app/Models/AcademicCalendar.php`
- `[NEW] app/Http/Controllers/AcademicCalendarController.php`
- `[NEW] database/migrations/xxxx_create_academic_calendar_table.php`
- `[NEW] resources/views/attendance/calendar/`
- `[MODIFY] app/Services/AttendanceService.php` — cek hari libur

---

## 🟡 Prioritas Sedang (Nice to Have)

### 5. 🧑‍🏫 Portal Guru/Wali Kelas yang Lebih Kaya
> Expand fitur wali kelas yang saat ini masih minimal

- **Dashboard per kelas** — statistik detail hanya untuk kelas yang diampu
- **Absensi per mata pelajaran** — guru bisa input absensi per jam pelajaran
- **Catatan perilaku siswa** — log catatan harian per siswa
- **Komunikasi langsung ke ortu** — kirim pesan WA per siswa dari dashboard wali

#### Proposed Changes
- `[MODIFY] app/Http/Controllers/WaliKelasController.php`
- `[NEW] resources/views/attendance/wali/dashboard-detail.blade.php`
- `[NEW] app/Models/StudentNote.php`

---

### 6. 🪪 Absensi Face Recognition (Opsional Lanjut)
> Alternatif selain QR code

- **Face detection** via kamera — pakai library JS (face-api.js)
- **Fallback ke QR** jika face tidak terdeteksi
- **Registrasi wajah** saat input data siswa
- **Anti-spoof** basic check

> [!WARNING]
> Fitur ini cukup kompleks dan membutuhkan storage tambahan untuk menyimpan face embeddings. Pertimbangkan resource server.

#### Proposed Changes
- `[NEW] app/Http/Controllers/FaceAttendanceController.php`
- `[NEW] resources/views/attendance/face/`
- `[MODIFY] database/migrations/` — tambah kolom face_encoding di students

---

### 7. 📤 Integrasi Rapor / Sistem Akademik
> Hubungkan data absensi ke sistem lain

- **API endpoint** — expose data absensi via REST API
- **Export format Dapodik** — format yang sesuai standar Kemendikbud
- **Webhook** — kirim data absensi ke sistem lain secara real-time

#### Proposed Changes
- `[NEW] app/Http/Controllers/Api/AttendanceApiController.php`
- `[NEW] routes/api.php` — public API routes
- `[MODIFY] config/cors.php`

---

### 8. 🏆 Gamifikasi Kehadiran
> Motivasi siswa dengan sistem reward

- **Poin kehadiran** — siswa dapat poin setiap hadir tepat waktu
- **Streak hadir** — bonus untuk hadir berturut-turut
- **Leaderboard** — ranking per kelas dan sekolah
- **Badge/Achievement** — "Perfect Week", "Rajin Bulan Ini", dll
- **Dashboard siswa** — siswa bisa lihat poin dan ranking mereka

#### Proposed Changes
- `[NEW] app/Models/StudentReward.php`
- `[NEW] app/Http/Controllers/GamificationController.php`
- `[NEW] resources/views/attendance/gamification/`
- `[NEW] database/migrations/xxxx_create_student_rewards_table.php`

---

## 🔵 Prioritas Rendah (Future Enhancement)

### 9. 📍 Geofencing / Lokasi-based Check-in
> Pastikan siswa check-in dari area sekolah

- **Set radius sekolah** di settings (latitude, longitude, radius)
- **Validasi lokasi GPS** saat scan QR
- **Log lokasi** untuk audit

---

### 10. 🤖 Chatbot WhatsApp Interaktif
> Ortu bisa chat bot WA untuk cek absensi

- Kirim "CEK [NIS]" → bot reply dengan status absensi hari ini
- Kirim "REKAP [NIS]" → bot reply rekap bulan ini
- Kirim "IZIN [NIS] [Tanggal] [Alasan]" → bot submit izin

---

### 11. 📊 Export & Sharing Canggih
- **Share laporan via WA** — PDF dikirim langsung ke wali kelas via WA
- **Scheduled export** — auto-generate dan kirim laporan mingguan
- **Custom report builder** — admin bisa pilih field dan filter sendiri

---

### 12. 🔐 Security & Audit
- **Audit trail** — log semua perubahan data (siapa, kapan, apa)
- **Two-factor auth** untuk admin
- **Session management** — lihat & logout device lain
- **IP whitelist** — batasi akses admin panel

---

## Open Questions

> [!IMPORTANT]
> Mohon pilih fitur mana yang ingin dikerjakan terlebih dahulu. Beberapa pertanyaan:

1. **Fitur mana yang paling dibutuhkan saat ini?** Pilih 1-3 dari daftar di atas.
2. **Apakah ada target waktu** (misal sebelum semester baru)?
3. **Apakah server saat ini mendukung** queue/scheduler Laravel? (untuk fitur notifikasi otomatis)
4. **Apakah ingin fitur absensi per mata pelajaran**, atau cukup absensi harian saja?

---

## Verification Plan

### Untuk setiap fitur yang dipilih:
- Migration & model testing via `php artisan migrate`
- Unit test untuk logic baru
- Browser testing untuk UI
- Integrasi test dengan WhatsApp gateway yang sudah ada
