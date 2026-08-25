# 📋 Plan Pengembangan — Face Recognition Attendance
> **Proyek:** Sistem Absensi SMK PGRI Blora  
> **Repo:** https://github.com/muochgack2-glitch/absensi  
> **Target:** Absensi wajah otomatis (tanpa QR) + fallback hybrid  
> **Server:** aaPanel spek tinggi

---

## 🌿 Strategi Branch

```
GitHub Repo
├── main     ← 🟢 PRODUKSI  (server live, siswa scan QR — tidak disentuh)
└── staging  ← 🔵 PENGEMBANGAN  (face recognition, aman dari produksi)
```

| | `main` | `staging` |
|---|---|---|
| Server | Produksi (live) | aaPanel folder terpisah |
| Database | `absensi` (data nyata) | `absensi_staging` (data dummy) |
| Boleh error? | ❌ | ✅ |
| Siapa akses | Siswa + guru | Developer + admin |

### Setup Branch
```bash
# Lokal — buat branch staging dari main
git checkout main && git pull origin main
git checkout -b staging
git push origin staging

# Server aaPanel — clone ke folder baru
cd /www/wwwroot
git clone -b staging https://github.com/muochgack2-glitch/absensi.git absensi_staging
```

---

## 🏆 Stack Teknologi Rekomendasi

> **Dengan aaPanel spek tinggi → InsightFace + FAISS + FastAPI**

```
Stack Lengkap:
├── Laravel (PHP)       ← sudah ada — web, absensi, notifikasi WA
├── FastAPI (Python)    ← SERVICE BARU — face recognition engine
│   ├── InsightFace     ← model AI pengenalan wajah (akurasi 99%+)
│   └── FAISS           ← vector search super cepat (Facebook AI)
└── MySQL               ← simpan face embedding per siswa
```

### Kenapa Stack Ini?

| Komponen | Fungsi | Performa Spek Tinggi |
|---|---|---|
| **InsightFace** `buffalo_l` | Ekstrak wajah → 512 angka (embedding) | ~30ms/foto (CPU multi-core) |
| **FAISS** `IndexFlatIP` | Cari 200 embedding dalam milidetik | ~5ms untuk 200 siswa |
| **FastAPI** | API Python async, sangat cepat | Handle banyak request sekaligus |
| **GPU (jika ada)** | Akselerasi InsightFace | <10ms per identifikasi |

### Perbandingan Teknologi

| | InsightFace+FAISS | CompreFace | face-api.js |
|---|---|---|---|
| **Akurasi** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Kecepatan (spek tinggi)** | ~135ms total | ~300ms | ~600ms |
| **Cocok spek tinggi** | ✅ Terbaik | ✅ Bagus | ❌ Overkill |
| **GPU support** | ✅ | ✅ | ❌ |
| **Kontrol penuh** | ✅ | ⚠️ terbatas | ✅ |

---

## 📸 Workflow 1 — Enrollment Wajah Siswa (Sekali Saja)

```
Admin buka halaman "Daftarkan Wajah" di profil siswa
        ↓
Kamera 2 aktif → siswa hadap kamera
        ↓
Ambil 5 foto wajah dari sudut sedikit berbeda
        ↓
[Browser] kirim 5 foto → [Laravel] → [FastAPI :8001]
        ↓
InsightFace ekstrak face embedding (512 float per foto)
        ↓
Rata-rata 5 embedding → 1 embedding final per siswa
        ↓
Simpan ke MySQL: attendance_students.face_embedding (JSON)
        ↓
FAISS index di-reload (tambahkan embedding siswa baru)
        ↓
✅ Siswa terdaftar — siap absen pakai wajah
```

---

## 🚪 Workflow 2 — Absensi Harian (Setiap Hari, Otomatis)

```
┌───────────────────────────────────────────────┐
│          LAYAR ABSENSI (welcome.blade.php)     │
│                                               │
│  [Kamera 1: QR (mode hybrid)]  [Kamera 2: Wajah Live]  │
│  ┌──────────────┐              ┌──────────────┐         │
│  │  QR standby  │              │  🔴 scanning │         │
│  └──────────────┘              └──────────────┘         │
└───────────────────────────────────────────────┘

ALUR FACE-ONLY (Opsi C):

1. Siswa berdiri di depan kamera
         ↓
2. Kamera 2 capture wajah otomatis (tiap ~1 detik)
         ↓
3. Browser kirim foto → Laravel POST /api/face-scan
         ↓
4. Laravel forward → FastAPI POST :8001/recognize
         ↓
5. InsightFace ekstrak embedding foto live
         ↓
6. FAISS search: "embedding ini paling mirip siapa?"
         ↓
   ┌──────────────────────────────────────┐
   │ Similarity > threshold (0.6)?        │
   │                                      │
   │  YA  → return { student_id, nama }   │
   │  TIDAK → "wajah tidak dikenal"       │
   └──────────────────────────────────────┘
         ↓
7. Laravel catat absen siswa yang dikenali
         ↓
8. Tentukan status: hadir / terlambat (cek jam)
         ↓
9. Popup muncul: "✅ Selamat datang, NAMA SISWA!"
         ↓
10. Kirim notifikasi WA ke orang tua + wali
```

---

## 🔄 Data Flow Detail

```
Browser (JavaScript)
    │ POST /api/face-scan  { photo: "base64..." }
    ▼
Laravel Controller (PHP)
    │ HTTP POST http://localhost:8001/recognize
    │           { image: "base64..." }
    ▼
FastAPI Service (Python)
    │
    ├─ InsightFace → detect & extract embedding [512 float]
    │
    ├─ FAISS.search(embedding, top_k=1)
    │     → [(student_id=45, similarity=0.87)]
    │
    └─ return {
            student_id : 45,
            nama       : "Budi Santoso",
            similarity : 0.87,
            status     : "recognized"   // atau "unknown"
       }
    ▼
Laravel Controller
    │
    ├─ similarity > threshold? → catat absen
    ├─ Tentukan status hadir/terlambat
    ├─ Simpan foto check_in_photo sebagai bukti
    └─ Return popup response ke browser
    ▼
Browser → tampilkan popup nama + status absen
```

---

## ⚡ Estimasi Kecepatan di Spek Tinggi

```
Capture foto dari kamera     50ms  ████
Kirim ke Laravel             20ms  ██
Laravel → FastAPI (LAN)      10ms  █
InsightFace extract face     30ms  ███
FAISS search 200 siswa        5ms  ▌
Return ke browser            10ms  █
Tampil popup                 10ms  █
                            ─────
TOTAL                       135ms  ✅ sangat cepat
```

> Jika pakai GPU Nvidia → estimasi total bisa < 50ms

---

## ⚙️ Mode Operasi (Pilih di Settings)

```
Mode Absensi            [Pengaturan di halaman Settings]
─────────────────────────────────────────────────────
○ QR Only          ← mode sekarang (tidak berubah)
○ Face Only        ← opsi C (siswa berdiri, langsung terdeteksi)
● QR + Face        ← opsi B hybrid (QR identifikasi, wajah verifikasi)
```

> Mode bisa diganti dari Settings tanpa ubah kode — hanya toggle di database.

---

## 🏗️ Arsitektur di aaPanel

```
aaPanel Server (spek tinggi)
│
├── Port 80/443   Nginx → Laravel (PHP)          ← web absensi (sudah ada)
│                         /www/wwwroot/absensi_staging
│
├── Port 8001     FastAPI (Python)               ← SERVICE BARU
│                         /www/wwwroot/face-service
│                         (dimanage Supervisor di aaPanel)
│
├── MySQL         absensi_staging                ← DB staging + face_embedding
│
└── Storage       /www/wwwroot/absensi_staging/storage/
                  ├── qr_codes/                  ← sudah ada
                  ├── attendance/photos/          ← sudah ada
                  └── face_enrollment/            ← BARU: foto referensi wajah
```

### Setup FastAPI di aaPanel (Supervisor)
```bash
# Install dependencies
pip install fastapi uvicorn insightface faiss-cpu numpy pillow python-multipart

# aaPanel → App Store → Supervisor → Add
[program:face-service]
command   = uvicorn main:app --host 0.0.0.0 --port 8001 --workers 4
directory = /www/wwwroot/face-service
autostart = true
autorestart = true
```

---

## 🗃️ Perubahan Database

```sql
-- Migration baru (staging saja dulu)
ALTER TABLE attendance_students
ADD COLUMN face_embedding   LONGTEXT NULL COMMENT 'JSON array 512 float InsightFace embedding',
ADD COLUMN face_enrolled_at TIMESTAMP NULL COMMENT 'Waktu enrollment wajah terakhir';
```

---

## 🔨 Komponen yang Perlu Dibangun

| | Komponen | Status |
|---|---|---|
| ✅ | Kamera 2 (foto wajah) | Sudah ada |
| ✅ | Dual webcam setup + settings | Sudah ada |
| ✅ | Laravel API + notifikasi WA | Sudah ada |
| ✅ | Popup absen + settings page | Sudah ada |
| ✅ | aaPanel server spek tinggi | Sudah ada |
| 🔨 | FastAPI service Python | Perlu dibuat |
| 🔨 | InsightFace + FAISS setup | Perlu dibuat |
| 🔨 | Halaman enrollment wajah siswa | Perlu dibuat |
| 🔨 | Migration `face_embedding` | Perlu dibuat |
| 🔨 | Laravel endpoint `/api/face-scan` | Perlu dibuat |
| 🔨 | FAISS index manager (load/reload) | Perlu dibuat |
| 🔨 | Setting mode absen di UI | Perlu dibuat |
| 🔨 | Supervisor config FastAPI | Perlu dibuat |

---

## 📅 Roadmap Per Fase (di branch `staging`)

### Fase 1 — Setup Infrastruktur
- [ ] Buat branch `staging` dari `main`
- [ ] Setup folder `absensi_staging` di aaPanel
- [ ] Install Python, FastAPI, InsightFace, FAISS di server
- [ ] Buat FastAPI service dasar (`/recognize`, `/enroll`, `/health`)
- [ ] Setup Supervisor aaPanel untuk FastAPI

### Fase 2 — Database & Enrollment
- [ ] Migration: tambah `face_embedding`, `face_enrolled_at`
- [ ] Halaman enrollment wajah di profil siswa
- [ ] Endpoint Laravel `/api/face-enroll` → forward ke FastAPI
- [ ] FAISS index builder (load semua embedding dari DB)

### Fase 3 — Absensi Face Scan
- [ ] Endpoint Laravel `/api/face-scan`
- [ ] Logika kamera 2 capture & kirim otomatis (JS)
- [ ] Integrasi hasil recognition ke flow absensi existing
- [ ] Setting mode: Face Only / QR+Face / QR Only

### Fase 4 — Testing & Polish
- [ ] Uji akurasi dengan siswa asli
- [ ] Tuning threshold similarity
- [ ] Liveness check (cegah foto dipotret foto)
- [ ] Fallback: jika wajah gagal → pakai QR

### Fase 5 — Merge ke Produksi
- [ ] Review lengkap di staging
- [ ] Merge `staging` → `main`
- [ ] Deploy + `php artisan migrate` di server produksi

---

## 📌 Catatan Penting

> [!IMPORTANT]
> Server produksi (`main`) **tidak boleh disentuh** selama pengembangan.
> Semua eksperimen hanya di branch `staging` dengan database `absensi_staging`.

> [!TIP]
> Mulai enrollment dari kelas yang kecil dulu (misal X AKL) untuk validasi akurasi sebelum roll out ke semua siswa.

> [!WARNING]
> **Face-only mode (C)** hanya bisa diaktifkan setelah **semua siswa** sudah enrollment wajah. Jika ada siswa yang belum, mereka tidak bisa absen. Rekomendasikan mode **QR + Face (B)** dulu sebagai transisi.

> [!NOTE]
> InsightFace model `buffalo_l` (~500MB) perlu didownload pertama kali. Pastikan server punya koneksi internet saat setup awal.
