# 📚 Dokumentasi — Sistem Absensi Siswa SMK PGRI Blora

Dokumen ini adalah **indeks ringkas** dari seluruh dokumentasi project. Dibuat untuk menggantikan puluhan file laporan sesi kerja (bug-fix report, migration log, ringkasan duplikat) yang sudah selesai/usang, agar dokumentasi lebih mudah dinavigasi.

> Riwayat perubahan detail per versi tetap ada di [`CHANGELOG.md`](CHANGELOG.md).

---

## 1. Gambaran Umum

Sistem absensi berbasis web menggunakan **QR Code** untuk SMK PGRI Blora. Siswa scan QR via kamera untuk check-in/check-out, dengan notifikasi WhatsApp otomatis ke orang tua.

**Tech stack:** Laravel 11, Vite, Bootstrap 5, MySQL, jsQR/qrcode-generator, Nginx + PHP-FPM. WhatsApp Gateway berjalan sebagai service Node.js terpisah (`whatsapp-server/`).

**Fitur utama:**
- Scan QR Code untuk check-in/check-out + capture foto otomatis
- Dashboard real-time monitoring kehadiran
- Notifikasi WhatsApp otomatis ke orang tua (check-in/out, alpha, keterlambatan)
- Manajemen kelas & siswa, termasuk import Excel
- Export laporan (Excel/PDF), termasuk kartu QR siswa dalam PDF (grid A4)
- Input manual absensi (tanpa scan, untuk situasi khusus)
- Continuous scan dengan hybrid feedback (toast + modal)
- Real-time update via polling (menggantikan SSE)

Setup lokal & deployment production: lihat [`README.md`](README.md). Panduan pemakaian lengkap untuk siswa/petugas/admin/wali kelas: lihat [`USER_MANUAL.md`](USER_MANUAL.md).

---

## 2. Peta Dokumentasi yang Masih Berlaku

### Panduan pengguna & setup
| File | Isi |
|---|---|
| [`README.md`](README.md) | Quick deploy, setup lokal, struktur folder, troubleshooting umum |
| [`USER_MANUAL.md`](USER_MANUAL.md) | Panduan lengkap per peran (siswa, petugas, admin, wali kelas), FAQ |
| [`PROJECT_SUMMARY.md`](PROJECT_SUMMARY.md) | Ringkasan eksekutif project, fitur yang sudah dikirim, tech stack |
| [`SEEDER_GUIDE.md`](SEEDER_GUIDE.md) | Cara pakai database seeder |
| [`SEED_X_BUSANA.md`](SEED_X_BUSANA.md) | Seeder spesifik untuk data kelas X Busana |
| [`CRON_SETUP.md`](CRON_SETUP.md) | Setup cron job untuk scheduled task Laravel |
| [`TESTING_CHECKLIST.md`](TESTING_CHECKLIST.md) | Checklist testing WhatsApp Gateway |

### Panduan fitur spesifik
| File | Isi |
|---|---|
| [`PANDUAN_FITUR_SETTINGS.md`](PANDUAN_FITUR_SETTINGS.md) | Pengaturan waktu, notifikasi WA, dan konfigurasi lain di halaman settings |
| [`PANDUAN_INPUT_MANUAL_ABSENSI.md`](PANDUAN_INPUT_MANUAL_ABSENSI.md) | Cara admin/guru menandai kehadiran tanpa scan QR |
| [`PHOTO_FEATURE_DOCUMENTATION.md`](PHOTO_FEATURE_DOCUMENTATION.md) | Fitur capture foto otomatis saat scan |
| [`LATE_WARNING_FEATURE.md`](LATE_WARNING_FEATURE.md) | Peringatan otomatis via WA untuk siswa yang sering terlambat |
| [`CONTINUOUS_SCAN_FEATURE.md`](CONTINUOUS_SCAN_FEATURE.md) | Scanner mendeteksi QR baru terus-menerus meski modal masih tampil |
| [`HYBRID_SCAN_FEEDBACK_SYSTEM.md`](HYBRID_SCAN_FEEDBACK_SYSTEM.md) | Sistem feedback scan (toast + modal) |
| [`POLLING_IMPLEMENTATION.md`](POLLING_IMPLEMENTATION.md) | Real-time update via polling di landing page, scanner, dashboard |
| [`REDESIGN_QR_BULK_PDF.md`](REDESIGN_QR_BULK_PDF.md) | Desain bulk generate QR jadi PDF kartu siswa (grid A4) |
| [`PDF_CARD_REDESIGN_DISCUSSION.md`](PDF_CARD_REDESIGN_DISCUSSION.md) | Diskusi desain layout kartu PDF |
| [`FITUR_QR_PDF_SUMMARY.md`](FITUR_QR_PDF_SUMMARY.md) | Ringkasan fitur download kartu QR PDF |
| [`TESTING_KARTU_QR_PDF.md`](TESTING_KARTU_QR_PDF.md) | Panduan testing fitur kartu QR PDF |

### WhatsApp Gateway
| File | Isi |
|---|---|
| [`WA_GATEWAY_CONTROL_FEATURE.md`](WA_GATEWAY_CONTROL_FEATURE.md) | Start/stop gateway via UI dashboard (PM2) |
| [`WA_GATEWAY_USER_GUIDE.md`](WA_GATEWAY_USER_GUIDE.md) | Panduan pemakaian gateway untuk admin |
| [`RESTART_WA_GATEWAY.md`](RESTART_WA_GATEWAY.md) | Cara restart gateway |
| [`UPDATE_WA_SERVER_KIRIM_GAMBAR.md`](UPDATE_WA_SERVER_KIRIM_GAMBAR.md) | Kemampuan gateway kirim gambar/foto sebagai lampiran pesan |

### Rencana ke depan
| File | Isi |
|---|---|
| [`FUTURE_PLAN.md`](FUTURE_PLAN.md) | Roadmap fitur lanjutan (analytics, notifikasi real-time, PWA, kalender akademik, face recognition, geofencing, dll), dikelompokkan per prioritas |

### Riwayat versi
| File | Isi |
|---|---|
| [`CHANGELOG.md`](CHANGELOG.md) | Log perubahan resmi per versi (format Keep a Changelog) |

---

## 3. Dokumen yang Sudah Dihapus (dan alasannya)

Sebagai referensi bila suatu saat perlu ditelusuri lagi lewat git history (`git log --diff-filter=D -- '*.md'`), berikut kategori 46 file yang dihapus karena sudah usang/duplikat:

- **Migrasi & housekeeping repo yang sudah selesai** (mis. migrasi ke SPMB stack, pemisahan repo Git) — historis, tidak relevan untuk pengembangan ke depan.
- **Laporan perbaikan bug satu kali** (mis. fix 404, fix CSRF, fix timezone) — bug sudah fixed dan tercatat ringkas di `CHANGELOG.md`.
- **Ringkasan duplikat** — versi "ringkasan"/"summary" dari dokumen yang isinya sama dengan panduan lengkapnya (contoh: `RINGKASAN_FITUR_SETTINGS.md` duplikat dari `PANDUAN_FITUR_SETTINGS.md`).
- **Planning/audit lama** (`sprint3_plan.md`, `upgrade_suggestions.md`, dll) — sudah digantikan oleh `FUTURE_PLAN.md` yang lebih terkini dan komprehensif.

Isi historis tersebut tetap dapat diakses lewat riwayat commit Git bila dibutuhkan.
