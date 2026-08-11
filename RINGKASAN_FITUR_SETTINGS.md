# 🎯 Ringkasan Fitur Settings - Quick Guide

## 4 Fitur Utama Settings

### 1️⃣ Pengaturan Waktu ⏰
**Fungsi:** Tentukan jam sekolah & toleransi keterlambatan

```
┌─────────────────────────────────────────────────┐
│  07:00         07:15         09:00      15:00   │
│    │             │             │           │     │
│  Masuk      Toleransi      Cutoff     Pulang    │
│              Habis          Alpha                │
│    │             │             │           │     │
│  [====HADIR====][==TERLAMBAT==][==ALPHA==]      │
│   ✅ Tepat Waktu  ⏰ Telat      ❌ Bolos         │
└─────────────────────────────────────────────────┘
```

**Pengaturan:**
- Jam Masuk: Kapan siswa mulai bisa absen
- Toleransi: Berapa menit masih dianggap hadir
- Batas Alpha: Lewat jam ini = otomatis alpha
- Jam Pulang: Kapan siswa bisa absen pulang

---

### 2️⃣ Notifikasi WhatsApp 💬
**Fungsi:** Kirim WA INSTANT saat siswa scan QR code

```
       Siswa Scan QR
            │
            ▼
    ┌───────────────┐
    │   Check-In?   │
    └───────┬───────┘
            │
    ┌───────┴───────┐
    ▼               ▼
  HADIR        TERLAMBAT
    │               │
    ▼               ▼
 WA: ✅          WA: ⏰
 
       Siswa Scan QR
            │
            ▼
    ┌───────────────┐
    │  Check-Out?   │
    └───────┬───────┘
            │
            ▼
         PULANG
            │
            ▼
         WA: ✅
```

**Pengaturan:**
- Toggle ON/OFF notifikasi
- Sertakan foto atau tidak
- Test kirim WA

**Kapan WA Terkirim:**
- ✅ Siswa scan masuk (HADIR/TERLAMBAT) → WA instant
- ✅ Siswa scan pulang → WA instant

---


### 3️⃣ Notifikasi Alpha Otomatis 🔴
**Fungsi:** Sistem AUTO kirim WA ke ortu siswa yang TIDAK HADIR

```
Timeline Harian:

07:00 ─────── 09:00 ─────── 09:00 (Jam Setting) ───────▶
  │              │              │
Jam Masuk    Cutoff       Cron Job Running
              Alpha        
                           ┌─────────────────┐
                           │ Cek Database:   │
                           │ Siapa saja yang │
                           │ belum absen?    │
                           └────────┬────────┘
                                    │
                      ┌─────────────┴─────────────┐
                      ▼                           ▼
                 Siswa A, B, C              Siswa D, E
                 Sudah Absen               Belum Absen
                      │                           │
                      ▼                           ▼
                 SKIP (OK)                   KIRIM WA ❌
                                            "Anak Anda ALPHA"
```

**Pengaturan:**
- 🔴 Toggle ON/OFF (merah)
- 🟡 Toggle Notifikasi Terlambat RT ON/OFF (kuning)
- ⏰ Jam Pengiriman (contoh: 09:00)
- 📅 Hari Aktif (Senin-Sabtu)
- ⚙️ Setup Cron Job (WAJIB!)

**Perbedaan dengan Fitur #2:**
| Aspek | Notifikasi Real-time (#2) | Notifikasi Alpha (#3) |
|-------|--------------------------|----------------------|
| Trigger | Siswa scan QR | Cron job terjadwal |
| Waktu | Instant (1-3 detik) | Sesuai jam setting |
| Status | HADIR/TERLAMBAT/PULANG | Hanya ALPHA |

**PENTING:** Butuh cron job di server!
```bash
* * * * * cd /www/wwwroot/absensi && php artisan schedule:run >> /dev/null 2>&1
```

---


### 4️⃣ Peringatan Keterlambatan ⚠️
**Fungsi:** Kirim WA PERINGATAN + STATISTIK untuk siswa yang SERING TERLAMBAT

```
Bulan Agustus - Riwayat Siswa Ahmad:

Tgl 5  │ Terlambat 20 menit  │ ❌ Tidak kirim (< 30 menit)
Tgl 6  │ Terlambat 35 menit  │ ❌ Tidak kirim (baru 1x)
Tgl 7  │ Terlambat 40 menit  │ ❌ Tidak kirim (baru 2x)
Tgl 8  │ Terlambat 50 menit  │ ✅ KIRIM PERINGATAN!
       │                     │    - 3x terlambat ✓
       │                     │    - ≥30 menit ✓
       └─────────────────────┘

WA yang Terkirim:
┌────────────────────────────────────────┐
│ ⚠️ PERINGATAN KETERLAMBATAN            │
│                                        │
│ Siswa: Ahmad Wijaya                   │
│ Kelas: X RPL 1                        │
│                                        │
│ 📊 STATISTIK BULAN INI:               │
│ • Total Terlambat: 3 kali             │
│ • Total Waktu Hilang: 125 menit       │
│ • Trend: 📈 Meningkat                 │
│                                        │
│ Detail Hari Ini:                      │
│ ⏰ Terlambat 50 menit (07:50)         │
└────────────────────────────────────────┘
```

**Pengaturan:**
- 🟠 Toggle ON/OFF (amber)
- ⏱️ Batas Keterlambatan (default: 30 menit)
- 🔢 Minimal Keterlambatan (default: 3 kali/bulan)

**Kondisi Peringatan Dikirim (AND logic):**
```
1. Keterlambatan hari ini ≥ 30 menit (threshold)
        AND
2. Total terlambat bulan ini ≥ 3x (min count)
        AND
3. Belum kirim peringatan hari ini (max 1x/hari)
        ↓
   KIRIM PERINGATAN ✅
```

**Statistik dalam Pesan:**
- 📊 Total berapa kali terlambat bulan ini
- ⏱️ Total berapa menit waktu hilang
- 📈 Trend: Meningkat/Menurun/Stabil

---


## 🎬 Skenario Lengkap: 1 Hari di Sekolah

### Skenario A: Siswa Rajin (Ahmad)
```
SETTING:
✅ Notifikasi WA: ON
✅ Sertakan Foto: ON
✅ Alpha Auto: ON (jam 09:00)
❌ Terlambat RT: OFF
✅ Peringatan Late: ON (30 menit, 3x)

TIMELINE AHMAD:
07:05 │ Scan QR Masuk
      ├─ Status: ✅ HADIR (dalam toleransi)
      └─ WA terkirim ke ortu: "✅ HADIR + foto"
      
15:00 │ Scan QR Pulang
      ├─ Status: ✅ PULANG
      └─ WA terkirim ke ortu: "✅ PULANG + foto"

TOTAL WA: 2 pesan (check-in + check-out)
```

### Skenario B: Siswa Terlambat Pertama Kali (Budi)
```
SETTING:
✅ Notifikasi WA: ON
❌ Sertakan Foto: OFF
✅ Alpha Auto: ON (jam 09:00)
✅ Terlambat RT: ON
✅ Peringatan Late: ON (30 menit, 3x)

TIMELINE BUDI:
07:35 │ Scan QR Masuk (terlambat 20 menit)
      ├─ Status: ⏰ TERLAMBAT
      ├─ WA #1: "⏰ TERLAMBAT 20 menit"
      └─ Cek kondisi peringatan:
          - Terlambat < 30 menit → ❌ Tidak kirim peringatan
          - Baru 1x terlambat bulan ini → ❌ Tidak kirim
          
15:10 │ Scan QR Pulang
      ├─ Status: ✅ PULANG
      └─ WA #2: "✅ PULANG"

TOTAL WA: 2 pesan (terlambat + pulang)
```

### Skenario C: Siswa Sering Terlambat (Citra)
```
SETTING:
✅ Notifikasi WA: ON
❌ Sertakan Foto: OFF
✅ Alpha Auto: ON (jam 09:00)
✅ Terlambat RT: ON
✅ Peringatan Late: ON (30 menit, 3x)

RIWAYAT CITRA BULAN INI:
- Tgl 5: Terlambat 35 menit
- Tgl 6: Terlambat 40 menit
- Tgl 7: Hadir tepat waktu
- Tgl 8: Terlambat 25 menit

TIMELINE HARI INI (Tgl 8):
07:50 │ Scan QR Masuk (terlambat 50 menit)
      ├─ Status: ⏰ TERLAMBAT
      ├─ WA #1: "⏰ TERLAMBAT 50 menit"
      └─ Cek kondisi peringatan:
          ✅ Terlambat ≥ 30 menit (50 > 30)
          ✅ Sudah 3x terlambat bulan ini (35+40+50)
          ✅ Belum kirim peringatan hari ini
          ↓
      ├─ WA #2: "⚠️ PERINGATAN + Statistik"
      │   • Total: 3x terlambat
      │   • Total waktu: 125 menit
      │   • Trend: 📈 Meningkat
          
15:00 │ Scan QR Pulang
      ├─ Status: ✅ PULANG
      └─ WA #3: "✅ PULANG"

TOTAL WA: 3 pesan (terlambat + peringatan + pulang)
```


### Skenario D: Siswa Alpha (Dedi)
```
SETTING:
✅ Notifikasi WA: ON
❌ Sertakan Foto: OFF
✅ Alpha Auto: ON (jam 09:00)
✅ Terlambat RT: ON
✅ Peringatan Late: ON (30 menit, 3x)

TIMELINE DEDI:
07:00 │ Jam Masuk
      └─ Dedi TIDAK scan QR
      
08:00 │ ...masih belum scan
      
09:00 │ Batas Cutoff Alpha
      ├─ Sistem otomatis marking: ❌ ALPHA
      └─ Cron job running...
      
09:00 │ Cron Job Kirim Notifikasi
      ├─ Sistem cek database
      ├─ Dedi belum absen → Status ALPHA
      └─ WA terkirim: "❌ ALPHA - Tidak Hadir"
      
15:00 │ Jam Pulang
      └─ Dedi tetap tidak scan (bolos seharian)

TOTAL WA: 1 pesan (alpha otomatis jam 09:00)
```

---

## 📊 Comparison Table

| Fitur | Trigger | Waktu | Status | Setup Cron? |
|-------|---------|-------|--------|-------------|
| **Notifikasi WA** | Scan QR | Instant | HADIR, TERLAMBAT, PULANG | ❌ Tidak |
| **Alpha Auto** | Cron Job | Terjadwal (09:00) | ALPHA saja | ✅ Wajib |
| **Terlambat RT** | Scan QR | Instant | TERLAMBAT saja | ❌ Tidak |
| **Peringatan Late** | Scan QR | Instant (kondisional) | TERLAMBAT (≥3x) | ❌ Tidak |

---

## 🎯 Rekomendasi Cepat

### Sekolah Kecil (< 200 siswa)
```
✅ Notifikasi WA: ON
❌ Sertakan Foto: OFF
✅ Alpha Auto: ON
❌ Terlambat RT: OFF
❌ Peringatan Late: OFF
```
**Alasan:** Simple, fokus pada kehadiran & alpha

### Sekolah Menengah (200-500 siswa)
```
✅ Notifikasi WA: ON
✅ Sertakan Foto: ON
✅ Alpha Auto: ON
✅ Terlambat RT: ON
✅ Peringatan Late: ON (30 menit, 3x)
```
**Alasan:** Full monitoring dengan foto sebagai bukti

### Sekolah Besar (> 500 siswa)
```
✅ Notifikasi WA: ON
❌ Sertakan Foto: OFF (hemat bandwidth)
✅ Alpha Auto: ON
❌ Terlambat RT: OFF
✅ Peringatan Late: ON (15 menit, 2x)
```
**Alasan:** Efisien, fokus pada pola bermasalah

---

## ⚠️ Penting Diingat!

### Toggle yang Perlu Cron Job:
- ✅ **Notifikasi Alpha Otomatis** → BUTUH CRON
- ❌ Notifikasi WA Real-time → TIDAK BUTUH CRON
- ❌ Terlambat RT → TIDAK BUTUH CRON
- ❌ Peringatan Late → TIDAK BUTUH CRON

### Cara Test Fitur:
```bash
# Test notifikasi real-time
→ Scan QR code langsung dari HP

# Test alpha auto
→ Tunggu sampai jam yang diset, atau:
→ php artisan attendance:mark-absent (manual trigger)

# Test peringatan late
→ Simulasi: siswa scan dengan status terlambat ≥3x
```

---

**📚 Dokumentasi Lengkap:** Lihat `PANDUAN_FITUR_SETTINGS.md`  
**🔧 Troubleshooting:** Cek `TOGGLE_FIX_SUMMARY.md`  
**💾 Last Updated:** Commit 56c4534 (11 Agustus 2026)
