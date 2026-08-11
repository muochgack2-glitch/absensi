# 📚 Panduan Lengkap Fitur Settings Sistem Absensi

## Daftar Isi
1. [Pengaturan Waktu](#1-pengaturan-waktu)
2. [Pengaturan Notifikasi WhatsApp](#2-pengaturan-notifikasi-whatsapp)
3. [Notifikasi Ketidakhadiran Otomatis](#3-notifikasi-ketidakhadiran-otomatis)
4. [Peringatan Keterlambatan](#4-peringatan-keterlambatan)

---

## 1. Pengaturan Waktu ⏰

### Fungsi
Mengatur jam operasional absensi dan toleransi keterlambatan siswa.

### Parameter

#### 1.1 Jam Masuk
- **Default:** 07:00
- **Fungsi:** Waktu mulai absensi masuk siswa
- **Contoh:** Jika diset 07:00, siswa bisa scan QR mulai jam 07:00

#### 1.2 Jam Pulang
- **Default:** 15:00
- **Fungsi:** Waktu mulai absensi pulang siswa
- **Contoh:** Jika diset 15:00, siswa bisa scan QR pulang mulai jam 15:00

#### 1.3 Toleransi Keterlambatan (menit)
- **Default:** 15 menit
- **Fungsi:** Batas waktu sebelum siswa dianggap terlambat
- **Rumus Status:**
  - `Jam Masuk` sampai `Jam Masuk + Toleransi` = ✅ **HADIR**
  - Setelah `Jam Masuk + Toleransi` = ⏰ **TERLAMBAT**

#### 1.4 Batas Waktu Alpha
- **Default:** 09:00
- **Fungsi:** Jika siswa belum scan sampai jam ini, otomatis ❌ **ALPHA**
- **Trigger:** Sistem otomatis marking alpha via cron job

### Contoh Timeline
```
07:00 - 07:15  →  ✅ HADIR (dalam toleransi)
07:16 - 09:00  →  ⏰ TERLAMBAT
Setelah 09:00  →  ❌ ALPHA (otomatis oleh sistem)
```


### Skenario Penggunaan

#### Skenario 1: Sekolah dengan Jam Masuk Pagi
**Kebutuhan:** Siswa masuk jam 07:00, toleransi 15 menit
```
- Jam Masuk: 07:00
- Toleransi: 15 menit
- Batas Alpha: 09:00
- Jam Pulang: 14:00

Timeline:
• 06:45 - Siswa scan QR → ✅ HADIR (boleh lebih awal)
• 07:10 - Siswa scan QR → ✅ HADIR (masih dalam toleransi)
• 07:30 - Siswa scan QR → ⏰ TERLAMBAT (lewat 15 menit)
• 09:15 - Siswa scan QR → ⏰ TERLAMBAT (tapi tetap dicatat, tidak alpha)
• 10:00 - Siswa belum scan → ❌ ALPHA (sistem otomatis)
```

#### Skenario 2: Sekolah dengan Toleransi Ketat
**Kebutuhan:** Disiplin tinggi, toleransi hanya 5 menit
```
- Jam Masuk: 06:30
- Toleransi: 5 menit
- Batas Alpha: 08:00
- Jam Pulang: 13:00

Timeline:
• 06:34 - Siswa scan QR → ✅ HADIR
• 06:36 - Siswa scan QR → ⏰ TERLAMBAT (lewat 5 menit!)
• 07:00 - Siswa scan QR → ⏰ TERLAMBAT
• 08:01 - Siswa belum scan → ❌ ALPHA
```

#### Skenario 3: Sekolah dengan Shift Siang
**Kebutuhan:** Kelas sore/malam
```
- Jam Masuk: 13:00
- Toleransi: 10 menit
- Batas Alpha: 14:00
- Jam Pulang: 18:00
```

---


## 2. Pengaturan Notifikasi WhatsApp 💬

### Fungsi
Mengatur notifikasi real-time saat siswa melakukan absensi (check-in/check-out).

### Parameter

#### 2.1 Kirim Notifikasi ke Orang Tua
- **Toggle:** ON/OFF
- **Default:** ON
- **Fungsi:** Aktifkan/matikan semua notifikasi WhatsApp
- **Jika OFF:** Sistem tidak akan kirim WA apapun meskipun siswa absen

#### 2.2 Sertakan Foto dalam Notifikasi
- **Toggle:** ON/OFF
- **Default:** OFF
- **Fungsi:** Kirim foto selfie siswa bersama pesan WA
- **Catatan:** Membutuhkan endpoint `/send-media` di WhatsApp Gateway
- **Ukuran File:** Foto otomatis di-resize jika terlalu besar

#### 2.3 Test Notifikasi
- **Fungsi:** Kirim test message ke nomor WA untuk cek koneksi
- **Format Nomor:** 628123456789 (harus pakai kode negara 62)
- **Gateway:** Default port 3002

### Jenis Notifikasi Real-time

#### A. Notifikasi Check-In (Masuk)
**Trigger:** Siswa scan QR code masuk
**Status yang dikirim:**
- ✅ **HADIR** - Jika dalam toleransi waktu
- ⏰ **TERLAMBAT** - Jika lewat toleransi

**Contoh Pesan:**
```
🎓 *NOTIFIKASI ABSENSI MASUK*

Nama: Ahmad Wijaya
Kelas: X RPL 1
Status: ✅ HADIR
Waktu: 07:10 WIB
Tanggal: Selasa, 11 Agustus 2026

Terima kasih atas perhatian Bapak/Ibu.
```

#### B. Notifikasi Check-Out (Pulang)
**Trigger:** Siswa scan QR code pulang
**Status:** Selalu ✅ PULANG

**Contoh Pesan:**
```
🏠 *NOTIFIKASI ABSENSI PULANG*

Nama: Ahmad Wijaya
Kelas: X RPL 1
Status: ✅ PULANG
Waktu: 15:05 WIB
Tanggal: Selasa, 11 Agustus 2026

Siswa telah pulang dari sekolah.
```


### Skenario Penggunaan

#### Skenario 1: Notifikasi Real-time Tanpa Foto
**Kebutuhan:** Ortu ingin tahu siswa sudah sampai sekolah
```
Pengaturan:
✅ Kirim Notifikasi ke Orang Tua: ON
❌ Sertakan Foto: OFF

Flow:
1. Siswa scan QR jam 07:10 → Status HADIR
2. Sistem langsung kirim WA ke nomor ortu
3. Ortu terima notifikasi dalam 1-3 detik
4. Pesan tanpa foto, hanya text informasi

Keuntungan:
- Pengiriman lebih cepat (tanpa upload foto)
- Hemat bandwidth
- Tetap informatif
```

#### Skenario 2: Notifikasi dengan Foto (Full Tracking)
**Kebutuhan:** Ortu ingin lihat bukti siswa benar-benar di sekolah
```
Pengaturan:
✅ Kirim Notifikasi ke Orang Tua: ON
✅ Sertakan Foto: ON

Flow:
1. Siswa scan QR + selfie jam 07:10
2. Sistem resize foto jika > 2MB
3. Sistem kirim WA dengan foto attachment
4. Ortu terima pesan + foto siswa

Keuntungan:
- Verifikasi visual (bukan orang lain yang scan)
- Anti kecurangan (titip absen)
- Ortu lebih yakin
```

#### Skenario 3: Matikan Semua Notifikasi
**Kebutuhan:** Maintenance sistem atau ujian nasional
```
Pengaturan:
❌ Kirim Notifikasi ke Orang Tua: OFF

Flow:
1. Siswa tetap bisa scan QR normal
2. Data absensi tetap tersimpan di database
3. TIDAK ADA WA yang terkirim ke ortu
4. Fitur lain (laporan, dashboard) tetap jalan

Use Case:
- Saat ujian (tidak mau ganggu ortu)
- Maintenance WhatsApp Gateway
- Event khusus (libur, hari raya)
```

---


## 3. Notifikasi Ketidakhadiran Otomatis 🔴

### Fungsi
Sistem otomatis mengirim WA ke orang tua pada jam tertentu setiap hari untuk memberitahu siswa yang **ALPHA** (tidak hadir tanpa keterangan).

### Perbedaan dengan Fitur #2
| Fitur | Notifikasi Real-time (#2) | Notifikasi Alpha Otomatis (#3) |
|-------|---------------------------|--------------------------------|
| **Trigger** | Saat siswa scan QR | Cron job (terjadwal) |
| **Waktu** | Instant (1-3 detik) | Sesuai jam yang diset |
| **Status** | HADIR, TERLAMBAT, PULANG | Hanya ALPHA |
| **Frekuensi** | Setiap ada scan | 1x per hari pada jam tertentu |

### Parameter

#### 3.1 Toggle Aktifkan
- **Toggle:** ON/OFF (merah)
- **Default:** OFF
- **Fungsi:** Aktifkan fitur auto-notify alpha
- **Jika ON:** Fields di bawah aktif (tidak blur)
- **Jika OFF:** Fields di bawah disabled (blur)

#### 3.2 Toggle Notifikasi Terlambat (Real-time)
- **Toggle:** ON/OFF (kuning)
- **Default:** OFF
- **Fungsi:** Kirim WA instant saat siswa scan dengan status **TERLAMBAT**
- **Catatan:** Ini tambahan dari notifikasi check-in biasa

#### 3.3 Jam Pengiriman Notifikasi Alpha
- **Default:** 09:00
- **Fungsi:** Waktu sistem kirim WA ke ortu siswa yang alpha
- **Rekomendasi:** Set setelah Batas Waktu Alpha
- **Contoh:** Jika Batas Alpha 09:00, set jam ini 09:00 atau 09:30

#### 3.4 Hari Aktif Pengiriman
- **Pilihan:** Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
- **Default:** Senin-Jumat (1,2,3,4,5)
- **Fungsi:** Tentukan hari mana saja notifikasi dikirim
- **Multi-select:** Bisa pilih lebih dari 1 hari

### Setup Cron Job (Wajib!)
**PENTING:** Fitur ini TIDAK AKAN JALAN tanpa cron job di server!

#### Langkah Setup:
```bash
# 1. SSH ke server
ssh user@server

# 2. Buka crontab
crontab -e

# 3. Tambahkan baris ini (sesuaikan path project)
* * * * * cd /www/wwwroot/absensi && php artisan schedule:run >> /dev/null 2>&1

# 4. Save & verifikasi
crontab -l
```

**Cara Kerja:**
- Cron job jalan setiap menit (*)
- Laravel scheduler cek: apakah sekarang jam yang ditentukan?
- Jika ya + hari aktif → kirim WA ke ortu siswa yang alpha
- Jika tidak → skip


### Contoh Pesan Alpha
```
⚠️ *NOTIFIKASI KETIDAKHADIRAN*

Yth. Orang Tua/Wali dari:
Nama: Ahmad Wijaya
Kelas: X RPL 1

Kami informasikan bahwa siswa tersebut:
Status: ❌ ALPHA (Tidak Hadir)
Tanggal: Selasa, 11 Agustus 2026

Siswa belum melakukan absensi hingga batas waktu yang ditentukan.

Mohon konfirmasi kepada kami jika ada keperluan/kondisi khusus.

Terima kasih.
```

### Skenario Penggunaan

#### Skenario 1: Notifikasi Alpha Setiap Hari Kerja
**Kebutuhan:** Ortu ingin tahu jika anak alpha
```
Pengaturan:
✅ Aktifkan Notifikasi Alpha Otomatis: ON
❌ Notifikasi Terlambat Real-time: OFF
⏰ Jam Pengiriman: 09:00
📅 Hari Aktif: Senin-Jumat

Flow Timeline:
• 07:00 - Jam masuk
• 07:15 - Batas toleransi
• 09:00 - Batas alpha (cutoff time)
• 09:00 - Cron job running:
  → Sistem cek database
  → Temukan 5 siswa belum absen
  → Kirim 5 WA ke ortu masing-masing
• 09:05 - Semua WA selesai terkirim

Hasil:
- Ortu langsung tahu jam 09:00 anaknya alpha
- Bisa langsung kontak anak/sekolah
- Admin tidak perlu manual satu-satu
```

#### Skenario 2: Notifikasi Terlambat + Alpha
**Kebutuhan:** Ortu mau tahu TERLAMBAT dan ALPHA
```
Pengaturan:
✅ Aktifkan Notifikasi Alpha Otomatis: ON
✅ Notifikasi Terlambat Real-time: ON
⏰ Jam Pengiriman: 09:30
📅 Hari Aktif: Senin-Jumat

Flow Timeline:
• 07:30 - Siswa A scan QR (terlambat 15 menit)
  → WA langsung terkirim: "⏰ TERLAMBAT"
• 08:15 - Siswa B scan QR (terlambat 1 jam)
  → WA langsung terkirim: "⏰ TERLAMBAT"
• 09:30 - Cron job running:
  → Siswa C, D, E belum scan
  → WA terkirim: "❌ ALPHA"

Keuntungan:
- Ortu tahu real-time saat anak terlambat
- Ortu juga tahu jam 09:30 jika anak alpha
- Monitoring lebih ketat
```


#### Skenario 3: Hanya Hari Senin & Jumat
**Kebutuhan:** Monitoring ketat awal & akhir minggu
```
Pengaturan:
✅ Aktifkan Notifikasi Alpha Otomatis: ON
⏰ Jam Pengiriman: 08:30
📅 Hari Aktif: ✅ Senin, ✅ Jumat

Flow:
• Senin 08:30 → Kirim WA siswa alpha
• Selasa 08:30 → TIDAK kirim WA (hari tidak aktif)
• Rabu 08:30 → TIDAK kirim WA
• Kamis 08:30 → TIDAK kirim WA
• Jumat 08:30 → Kirim WA siswa alpha
• Sabtu → Libur (tidak ada notifikasi)

Use Case:
- Hemat kuota WA Gateway
- Fokus monitoring awal & akhir minggu
- Ortu tidak terlalu banyak terima notifikasi
```

#### Skenario 4: Sekolah dengan Jadwal 6 Hari
**Kebutuhan:** Sekolah masuk Senin-Sabtu
```
Pengaturan:
✅ Aktifkan Notifikasi Alpha Otomatis: ON
⏰ Jam Pengiriman: 09:00
📅 Hari Aktif: Senin-Sabtu (semua dicentang)

Flow:
- Notifikasi alpha terkirim setiap hari jam 09:00
- Minggu tetap off (tidak ada pilihan Minggu)
- Cocok untuk pondok pesantren atau SMK
```

---


## 4. Peringatan Keterlambatan ⚠️

### Fungsi
Sistem otomatis mengirim **peringatan khusus** ke orang tua jika siswa **sering terlambat** dalam sebulan. Ini adalah notifikasi tambahan dengan statistik lengkap.

### Perbedaan dengan Fitur Lain
| Fitur | Notifikasi Real-time | Notifikasi Alpha | Peringatan Keterlambatan |
|-------|---------------------|------------------|--------------------------|
| **Target** | Semua siswa | Siswa alpha | Siswa sering terlambat |
| **Trigger** | Saat scan QR | Cron job harian | Real-time saat scan |
| **Kondisi** | - | Belum absen | ≥X kali terlambat |
| **Isi Pesan** | Status saja | Alpha info | Statistik lengkap |
| **Tujuan** | Informasi | Peringatan alpha | Peringatan disiplin |

### Kapan Peringatan Dikirim?
**Trigger:** Saat siswa scan QR dengan status **TERLAMBAT** (real-time)

**Kondisi (AND logic):**
1. Keterlambatan saat ini ≥ Threshold (default 30 menit)
2. Total keterlambatan bulan ini ≥ Min Count (default 3x)
3. Belum pernah kirim peringatan hari ini (max 1x per hari)

### Parameter

#### 4.1 Toggle Aktifkan
- **Toggle:** ON/OFF (amber)
- **Default:** OFF
- **Fungsi:** Aktifkan sistem peringatan keterlambatan
- **Jika OFF:** Peringatan tidak akan dikirim meskipun siswa sering terlambat

#### 4.2 Batas Keterlambatan (menit)
- **Default:** 30 menit
- **Range:** 1-120 menit
- **Fungsi:** Minimal berapa menit terlambat baru trigger peringatan
- **Contoh:** Jika set 30, maka keterlambatan 25 menit tidak trigger

#### 4.3 Jumlah Minimal Keterlambatan
- **Default:** 3 kali
- **Range:** 1-20 kali
- **Fungsi:** Minimal berapa kali terlambat dalam 1 bulan baru trigger
- **Reset:** Otomatis reset setiap awal bulan

### Statistik dalam Pesan
Pesan peringatan mengandung data analitik:

1. **Total Keterlambatan:** Berapa kali terlambat bulan ini
2. **Akumulasi Waktu:** Total menit yang hilang karena terlambat
3. **Trend:** Apakah keterlambatan meningkat/menurun/stabil
   - 📈 **Meningkat:** 2 keterlambatan terakhir > rata-rata
   - 📉 **Menurun:** 2 keterlambatan terakhir < rata-rata
   - ➡️ **Stabil:** Tidak ada pola signifikan


### Contoh Pesan Peringatan
```
⚠️ *PERINGATAN KETERLAMBATAN*

Yth. Orang Tua/Wali dari:
Nama: Ahmad Wijaya
Kelas: X RPL 1

📊 STATISTIK KETERLAMBATAN BULAN INI:
• Total Terlambat: 5 kali
• Total Waktu Hilang: 180 menit (3 jam)
• Trend: 📈 Meningkat

Kami mohon perhatian Bapak/Ibu untuk membimbing putra/putri agar lebih disiplin waktu.

Detail hari ini:
⏰ Terlambat 45 menit (07:45)
Tanggal: Selasa, 11 Agustus 2026

Terima kasih atas kerjasamanya.
```

### Skenario Penggunaan

#### Skenario 1: Peringatan Standard (Default)
**Kebutuhan:** Kirim peringatan untuk siswa yang sering terlambat
```
Pengaturan:
✅ Aktifkan Peringatan: ON
⏱️ Batas Keterlambatan: 30 menit
🔢 Minimal Keterlambatan: 3 kali

Timeline Siswa A (Bulan Agustus):
• 5 Agt - Terlambat 20 menit → ❌ Tidak trigger (< 30 menit)
• 6 Agt - Terlambat 35 menit → ❌ Tidak trigger (baru 1x bulan ini)
• 7 Agt - Terlambat 40 menit → ❌ Tidak trigger (baru 2x bulan ini)
• 8 Agt - Terlambat 50 menit → ✅ TRIGGER! (3x + ≥30 menit)
  → WA peringatan terkirim dengan statistik lengkap
• 9 Agt - Terlambat 60 menit → ❌ Tidak trigger (sudah kirim hari ini)
• 10 Agt - Terlambat 45 menit → ✅ TRIGGER! (hari baru, masih ≥3x)

Hasil:
- Ortu sadar anak sering terlambat
- Ada data konkrit (5x, 180 menit)
- Bisa ambil tindakan preventif
```

#### Skenario 2: Peringatan Ketat (Disiplin Tinggi)
**Kebutuhan:** Sekolah dengan standar disiplin tinggi
```
Pengaturan:
✅ Aktifkan Peringatan: ON
⏱️ Batas Keterlambatan: 15 menit
🔢 Minimal Keterlambatan: 2 kali

Timeline Siswa B (Bulan Agustus):
• 5 Agt - Terlambat 10 menit → ❌ Tidak trigger (< 15 menit)
• 6 Agt - Terlambat 20 menit → ❌ Tidak trigger (baru 1x)
• 7 Agt - Terlambat 18 menit → ✅ TRIGGER! (2x + ≥15 menit)
  → Peringatan terkirim

Keuntungan:
- Deteksi dini siswa bermasalah
- Ortu langsung aware sejak 2x terlambat
- Mencegah kebiasaan buruk berkembang
```


#### Skenario 3: Peringatan Longgar (Toleran)
**Kebutuhan:** Sekolah di daerah transportasi sulit
```
Pengaturan:
✅ Aktifkan Peringatan: ON
⏱️ Batas Keterlambatan: 60 menit
🔢 Minimal Keterlambatan: 5 kali

Timeline Siswa C:
• Bulan Agustus - Terlambat 4x (30-50 menit)
  → ❌ Tidak trigger (< 5x)
• 5 hari ke-depan - Terlambat lagi 70 menit
  → ✅ TRIGGER! (5x + ≥60 menit)

Use Case:
- Daerah pegunungan/transportasi terbatas
- Fokus pada pola buruk, bukan insiden tunggal
- Ortu hanya terima peringatan jika benar-benar kronis
```

#### Skenario 4: Non-Aktif (Tanpa Peringatan)
**Kebutuhan:** Sekolah tidak mau sistem otomatis kirim peringatan
```
Pengaturan:
❌ Aktifkan Peringatan: OFF

Hasil:
- Peringatan keterlambatan tidak pernah dikirim
- Notifikasi real-time (TERLAMBAT) tetap jalan
- Notifikasi alpha tetap jalan
- Admin bisa lihat statistik di dashboard
- Ortu hanya terima notifikasi per-absensi, bukan peringatan

Use Case:
- Sekolah prefer laporan bulanan manual
- Tidak mau "menghakimi" siswa otomatis
- Komunikasi ortu lebih personal
```

---


## 5. Kombinasi Fitur (Advanced Scenarios) 🚀

### Kombinasi 1: Full Monitoring (Semua Aktif)
**Profile:** Sekolah dengan disiplin ketat, ortu mau tahu semua
```
Pengaturan:
✅ Notifikasi ke Orang Tua: ON
✅ Sertakan Foto: ON
✅ Notifikasi Alpha Otomatis: ON
✅ Notifikasi Terlambat Real-time: ON
✅ Peringatan Keterlambatan: ON

Flow Harian Siswa:
• 07:05 - Scan QR masuk (HADIR)
  → WA instant: "✅ HADIR + foto"
• 15:00 - Scan QR pulang
  → WA instant: "✅ PULANG + foto"

Flow Jika Terlambat (Hari ke-3):
• 07:40 - Scan QR (TERLAMBAT 25 menit)
  → WA 1: "⏰ TERLAMBAT + foto"
  → WA 2: "⚠️ PERINGATAN (3x terlambat, 90 menit total)"
  → 2 WA terkirim dalam 5 detik

Flow Jika Alpha:
• 09:00 - Belum scan sampai cutoff
  → Cron job running
  → WA: "❌ ALPHA"

Keuntungan:
- Ortu tahu semua aktivitas real-time
- Ada bukti foto setiap absen
- Deteksi dini siswa bermasalah
- Komunikasi transparan sekolah-ortu

Tantangan:
- Ortu terima banyak WA (bisa 3-4 WA per hari)
- Butuh WhatsApp Gateway stabil
- Bandwidth tinggi (karena foto)
```

### Kombinasi 2: Minimal Notification (Efisien)
**Profile:** Sekolah kecil, ortu tidak mau terlalu banyak WA
```
Pengaturan:
✅ Notifikasi ke Orang Tua: ON
❌ Sertakan Foto: OFF
❌ Notifikasi Alpha Otomatis: OFF
❌ Notifikasi Terlambat Real-time: OFF
❌ Peringatan Keterlambatan: OFF

Flow:
• Siswa scan masuk → WA: "✅ HADIR" atau "⏰ TERLAMBAT"
• Siswa scan pulang → WA: "✅ PULANG"
• Tidak ada WA otomatis lain

Keuntungan:
- Simple, tidak overwhelming
- Ortu tetap tahu siswa sudah sampai/pulang
- Hemat kuota & bandwidth
- WhatsApp Gateway tidak terbebani

Use Case:
- SD/MI dengan ortu kurang tech-savvy
- Daerah dengan sinyal terbatas
- Focus: konfirmasi kehadiran saja
```


### Kombinasi 3: Focus Alpha & Late Warning
**Profile:** Sekolah fokus pada siswa bermasalah
```
Pengaturan:
✅ Notifikasi ke Orang Tua: ON
❌ Sertakan Foto: OFF
✅ Notifikasi Alpha Otomatis: ON
❌ Notifikasi Terlambat Real-time: OFF
✅ Peringatan Keterlambatan: ON

Timeline:
• Siswa HADIR → ❌ TIDAK kirim WA (hemat notifikasi)
• Siswa PULANG → ❌ TIDAK kirim WA
• Siswa ALPHA (09:00) → ✅ Kirim WA via cron
• Siswa TERLAMBAT 3x+ → ✅ Kirim WA peringatan

Keuntungan:
- Ortu hanya terima WA jika ada masalah
- Tidak spam WA untuk siswa rajin
- Deteksi cepat pola buruk (alpha/late)
- Admin fokus handle siswa bermasalah

Use Case:
- SMA/SMK dengan siswa mandiri
- Ortu sibuk, hanya mau tahu jika ada masalah
- Strategi "silent monitoring"
```

### Kombinasi 4: Real-time Only (No Scheduled)
**Profile:** Sekolah dengan admin aktif, tidak mau cron job
```
Pengaturan:
✅ Notifikasi ke Orang Tua: ON
✅ Sertakan Foto: ON
❌ Notifikasi Alpha Otomatis: OFF
✅ Notifikasi Terlambat Real-time: ON
❌ Peringatan Keterlambatan: OFF

Flow:
• Semua notifikasi triggered by user action (scan QR)
• Tidak ada cron job/scheduled task
• Admin manual handle siswa alpha
• Ortu terima WA instant saat scan

Keuntungan:
- Tidak perlu setup cron job
- Tidak ada scheduled task di background
- Full control di tangan admin
- Suitable untuk shared hosting tanpa cron access

Use Case:
- Hosting murah tanpa cron support
- Sekolah prefer kontrol manual
- Testing/development environment
```

---


## 6. Troubleshooting & FAQ 🔧

### Q1: WA tidak terkirim sama sekali
**Cek:**
1. ✅ Toggle "Kirim Notifikasi ke Orang Tua" ON?
2. ✅ WhatsApp Gateway running? (cek PM2: `pm2 list`)
3. ✅ Nomor ortu format benar? (62xxx, bukan 08xxx)
4. ✅ WhatsApp Gateway authenticated? (scan QR lagi)
5. ✅ Cek log error: `tail -f storage/logs/laravel.log`

**Test:**
```bash
# Test koneksi gateway
curl http://localhost:3002/status

# Test kirim manual
php artisan tinker
>>> \App\Services\AttendanceNotificationService::test('628123456789');
```

### Q2: Notifikasi Alpha tidak jalan (padahal sudah setup cron)
**Cek:**
1. ✅ Toggle "Aktifkan Notifikasi Alpha Otomatis" ON?
2. ✅ Hari ini termasuk hari aktif?
3. ✅ Cron job sudah setup di server? (`crontab -l`)
4. ✅ Cron running? (`grep CRON /var/log/syslog`)
5. ✅ Laravel scheduler working? (`php artisan schedule:list`)

**Manual Trigger Test:**
```bash
# Jalankan manual (di production server)
cd /www/wwwroot/absensi
php artisan attendance:mark-absent

# Cek log
tail -f storage/logs/laravel.log
```

### Q3: Peringatan keterlambatan tidak muncul padaoh siswa sudah 5x terlambat
**Cek:**
1. ✅ Toggle "Peringatan Keterlambatan" ON?
2. ✅ Keterlambatan ≥ Batas Menit? (default 30 menit)
3. ✅ Jumlah keterlambatan ≥ Min Count? (default 3x)
4. ✅ Sudah kirim peringatan hari ini? (max 1x per hari)
5. ✅ Keterlambatan dalam bulan ini atau bulan lalu?

**Debug:**
```bash
php artisan tinker

# Cek total keterlambatan bulan ini
$student = \App\Models\AttendanceStudent::find(1);
$count = \App\Models\Attendance::where('student_id', $student->id)
    ->where('status', 'terlambat')
    ->whereMonth('date', now()->month)
    ->count();
echo "Total terlambat: $count\n";

# Cek menit keterlambatan terakhir
$last = \App\Models\Attendance::where('student_id', $student->id)
    ->where('status', 'terlambat')
    ->latest('check_in_time')
    ->first();
echo "Terlambat: {$last->late_duration} menit\n";
```


### Q4: Toggle tidak ON setelah disimpan dan refresh
**Solusi:** Sudah fixed di commit `56c4534`

Jika masih bermasalah:
```bash
cd /www/wwwroot/absensi
git pull origin main
php artisan view:clear
php artisan config:clear
```

### Q5: Foto tidak terkirim meskipun toggle ON
**Cek:**
1. ✅ WhatsApp Gateway support `/send-media`?
2. ✅ Endpoint tersedia? (`curl http://localhost:3002/send-media`)
3. ✅ Foto tersimpan di storage? (`ls -lh storage/app/public/attendance_photos`)
4. ✅ Symlink storage sudah dibuat? (`php artisan storage:link`)
5. ✅ File size tidak terlalu besar? (max 2MB per foto)

**Update Gateway:**
```bash
cd /path/to/whatsapp-server
npm install multer
# Update server.js dengan endpoint /send-media
pm2 restart wa-spmb
```

### Q6: Cron job tidak jalan di shared hosting
**Alternatif:**
1. Gunakan cPanel Cron Jobs (jika tersedia)
2. Setup external cron service (cron-job.org, easycron.com)
3. Hit endpoint manual via curl:
```
* * * * * curl https://yoursite.com/api/attendance/cron-trigger
```
4. Disable fitur alpha auto, gunakan manual marking

### Q7: Notifikasi lambat/delay
**Optimasi:**
1. Gunakan Queue untuk background processing:
```bash
php artisan queue:work --daemon
```
2. Upgrade WhatsApp Gateway ke server terpisah
3. Implement Redis/database queue driver
4. Optimize foto (resize lebih kecil)
5. Disable foto jika tidak perlu

### Q8: Ortu komplain terlalu banyak WA
**Solusi:**
1. Matikan "Notifikasi Terlambat Real-time"
2. Matikan "Peringatan Keterlambatan"
3. Kurangi hari aktif notifikasi alpha
4. Implementasi setting per-ortu (future feature)
5. Kirim summary harian instead of real-time

---


## 7. Best Practices & Recommendations 💡

### Rekomendasi Setting untuk Jenis Sekolah

#### SD/MI (Sekolah Dasar)
```
⏰ Pengaturan Waktu:
- Jam Masuk: 07:00
- Toleransi: 15 menit
- Batas Alpha: 08:00
- Jam Pulang: 12:00

💬 Notifikasi:
✅ Kirim Notifikasi: ON
✅ Sertakan Foto: ON (ortu suka lihat anaknya)
✅ Notifikasi Alpha: ON (jam 08:30)
❌ Notifikasi Terlambat RT: OFF (anak kecil sering telat wajar)
❌ Peringatan Keterlambatan: OFF

Alasan:
- Ortu SD sangat concern keamanan anak
- Foto sebagai bukti anak sampai sekolah
- Toleransi untuk urusan anak kecil (pipis, nangis, dll)
- Focus: kehadiran & keselamatan
```

#### SMP/MTs
```
⏰ Pengaturan Waktu:
- Jam Masuk: 06:45
- Toleransi: 10 menit
- Batas Alpha: 08:30
- Jam Pulang: 14:30

💬 Notifikasi:
✅ Kirim Notifikasi: ON
❌ Sertakan Foto: OFF (hemat bandwidth)
✅ Notifikasi Alpha: ON (jam 09:00)
✅ Notifikasi Terlambat RT: ON
✅ Peringatan Keterlambatan: ON (30 menit, 3x)

Alasan:
- Mulai melatih disiplin waktu
- Ortu masih perlu monitoring ketat
- Deteksi pola buruk sejak dini
- Balance antara monitoring & kemandirian
```

#### SMA/SMK/MA
```
⏰ Pengaturan Waktu:
- Jam Masuk: 06:30
- Toleransi: 5 menit
- Batas Alpha: 09:00
- Jam Pulang: 15:30

💬 Notifikasi:
✅ Kirim Notifikasi: ON
❌ Sertakan Foto: OFF
✅ Notifikasi Alpha: ON (jam 09:30)
❌ Notifikasi Terlambat RT: OFF
✅ Peringatan Keterlambatan: ON (15 menit, 2x)

Alasan:
- Disiplin tinggi (persiapan dunia kerja)
- Ortu fokus pada pola, bukan insiden
- Siswa sudah mandiri (foto tidak perlu)
- Peringatan ketat (threshold rendah)
```

#### Pondok Pesantren
```
⏰ Pengaturan Waktu:
- Jam Masuk: 05:00 (subuh)
- Toleransi: 5 menit
- Batas Alpha: 06:00
- Jam Pulang: 21:00 (isya)

💬 Notifikasi:
✅ Kirim Notifikasi: ON
❌ Sertakan Foto: OFF
✅ Notifikasi Alpha: ON (jam 06:30)
❌ Notifikasi Terlambat RT: OFF
✅ Peringatan Keterlambatan: ON (10 menit, 2x)
📅 Hari Aktif: Senin-Sabtu

Alasan:
- Disiplin syar'i tinggi
- Santri tinggal di pondok (ortu jarak jauh)
- Monitoring ketat untuk ibadah
- Laporan rutin ke wali santri
```


### Tips Optimasi WhatsApp Gateway

#### 1. Load Balancing (Multiple Gateways)
```
Scenario: 500+ siswa, notifikasi peak time (07:00-08:00)

Setup:
- Gateway 1 (Port 3000): Handle Kelas X
- Gateway 2 (Port 3001): Handle Kelas XI  
- Gateway 3 (Port 3002): Handle Kelas XII

Keuntungan:
- Tidak ada bottleneck
- Jika 1 gateway down, yang lain backup
- Bisa pakai multiple WA number
```

#### 2. Queue System
```bash
# Setup Queue Worker
php artisan queue:table
php artisan migrate
php artisan queue:work --tries=3

# Update .env
QUEUE_CONNECTION=database

# Setup PM2 untuk queue worker
pm2 start "php artisan queue:work" --name absensi-queue
```

#### 3. Rate Limiting
```php
// Batasi max 10 WA per detik
// Prevent banned by WhatsApp
// Implementasi di AttendanceNotificationService
```

#### 4. Failover Strategy
```
Primary: Gateway SPMB (Port 3000)
Backup: Gateway Absensi (Port 3001)

Logic:
1. Try send via primary
2. If fail → auto-switch to backup
3. Log failure for admin review
4. Retry failed messages every 5 minutes
```

### Timeline Implementasi Bertahap

#### Minggu 1: Setup Dasar
```
✅ Install & konfigurasi WhatsApp Gateway
✅ Aktifkan notifikasi real-time (tanpa foto)
✅ Test dengan 1 kelas pilot
✅ Training guru & admin
✅ Sosialisasi ke ortu
```

#### Minggu 2: Add Features
```
✅ Aktifkan foto dalam notifikasi
✅ Setup cron job untuk alpha auto
✅ Test notifikasi alpha di hari Senin
✅ Monitor performa & bottleneck
✅ Adjust setting berdasarkan feedback
```

#### Minggu 3: Advanced Features
```
✅ Aktifkan notifikasi terlambat real-time
✅ Aktifkan peringatan keterlambatan
✅ Setup queue system
✅ Implementasi failover gateway
✅ Full deployment ke semua kelas
```

#### Minggu 4: Monitoring & Optimization
```
✅ Review statistik keterlambatan
✅ Analisa feedback ortu
✅ Optimasi setting (threshold, jam, hari)
✅ Dokumentasi best practices
✅ Training lanjutan untuk wali kelas
```

---


## 8. Decision Tree: Setting yang Tepat untuk Anda 🌳

```
START: Pilih setting yang tepat

┌─ Apakah ortu mau terima WA saat siswa hadir normal?
│  ├─ YA → ✅ Kirim Notifikasi: ON
│  │       └─ Apakah butuh bukti foto?
│  │          ├─ YA → ✅ Sertakan Foto: ON (SD/MI cocok)
│  │          └─ TIDAK → ❌ Sertakan Foto: OFF (SMP+ cocok)
│  │
│  └─ TIDAK → ✅ Kirim Notifikasi: ON, tapi...
│             └─ ❌ Matikan semua auto-notify
│                 └─ Ortu hanya terima WA manual dari admin
│
├─ Apakah butuh monitoring siswa alpha otomatis?
│  ├─ YA → ✅ Notifikasi Alpha Otomatis: ON
│  │       ├─ Jam berapa mau kirim? → Set Jam Pengiriman
│  │       ├─ Hari apa saja? → Pilih Hari Aktif
│  │       └─ ⚠️ WAJIB setup cron job!
│  │
│  └─ TIDAK → ❌ Notifikasi Alpha: OFF
│             └─ Admin manual tandai & kontak ortu
│
├─ Apakah mau ortu tahu INSTANT saat anak terlambat?
│  ├─ YA → ✅ Notifikasi Terlambat RT: ON
│  │       └─ Ortu terima WA dalam 1-3 detik
│  │
│  └─ TIDAK → ❌ Notifikasi Terlambat RT: OFF
│             └─ Cukup lihat laporan akhir bulan
│
└─ Apakah butuh sistem peringatan otomatis untuk siswa sering terlambat?
   ├─ YA → ✅ Peringatan Keterlambatan: ON
   │       ├─ Berapa menit minimal? → Set Threshold (15-60)
   │       ├─ Berapa kali minimal? → Set Min Count (2-5)
   │       └─ Pesan include statistik lengkap
   │
   └─ TIDAK → ❌ Peringatan Keterlambatan: OFF
              └─ Guru/BK handle manual

END: Setting optimal untuk kebutuhan Anda! ✅
```

---

## 9. Rangkuman Cepat (Cheat Sheet) 📋

### Fitur 1: Pengaturan Waktu ⏰
- **Fungsi:** Atur jam operasional & toleransi
- **Trigger:** Sistem
- **Impact:** Menentukan status HADIR/TERLAMBAT/ALPHA

### Fitur 2: Notifikasi WhatsApp 💬
- **Fungsi:** WA real-time saat siswa scan QR
- **Trigger:** User action (scan QR)
- **Impact:** Ortu tahu siswa sampai/pulang
- **Include:** Check-in & check-out notifications

### Fitur 3: Notifikasi Alpha Otomatis 🔴
- **Fungsi:** WA terjadwal untuk siswa yang tidak hadir
- **Trigger:** Cron job (scheduled)
- **Impact:** Ortu tahu anak alpha
- **Require:** Cron job setup

### Fitur 4: Peringatan Keterlambatan ⚠️
- **Fungsi:** WA peringatan + statistik untuk siswa sering terlambat
- **Trigger:** Real-time saat scan QR (dengan kondisi)
- **Impact:** Deteksi dini pola buruk
- **Include:** Total count, total menit, trend

### Quick Reference: Status & Icons
```
✅ HADIR         - On time check-in
⏰ TERLAMBAT    - Late check-in
❌ ALPHA         - No check-in (absent)
✅ PULANG        - Check-out
🏥 IZIN          - Excused absence
🏥 SAKIT         - Sick leave
```

### Quick Reference: Toggle Colors
```
🟢 Hijau  - Notifikasi umum
🔴 Merah   - Notifikasi alpha
🟡 Kuning  - Notifikasi terlambat RT
🟠 Amber   - Peringatan keterlambatan
```

---

**Dokumen ini dibuat:** 11 Agustus 2026  
**Versi Sistem:** v2.0 (dengan Late Warning feature)  
**Last Updated:** Commit 56c4534

**Butuh bantuan?** Hubungi admin sistem atau cek log error di `storage/logs/laravel.log`
