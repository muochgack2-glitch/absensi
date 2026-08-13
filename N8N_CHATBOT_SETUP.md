# 🤖 Setup n8n Chatbot untuk Wali Kelas

## 📋 Overview

Fitur chatbot WhatsApp interaktif untuk wali kelas, memungkinkan mereka request ringkasan absensi dengan mengirim pesan WhatsApp.

**Contoh Penggunaan:**
```
Wali Kelas kirim WA → "ringkasan kehadiran hari ini"
Bot balas → Detail ringkasan absensi kelas hari ini
```

---

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────┐
│  Wali Kelas kirim WA:                   │
│  "ringkasan kehadiran hari ini"         │
└────────────────┬────────────────────────┘
                 ↓
┌─────────────────────────────────────────┐
│    WhatsApp Gateway (server.js)         │
│  • Terima pesan masuk                   │
│  • Forward ke n8n webhook               │
└────────────────┬────────────────────────┘
                 ↓
┌─────────────────────────────────────────┐
│         n8n Workflow                     │
│  1. Parse keyword (ringkasan/help)      │
│  2. Query Laravel API                   │
│  3. Format response                     │
│  4. Kirim balik ke WA Gateway           │
└────────────────┬────────────────────────┘
                 ↓
┌─────────────────────────────────────────┐
│    Laravel API                          │
│  • Identify wali kelas by phone         │
│  • Get attendance data                  │
│  • Return JSON                          │
└─────────────────────────────────────────┘
```

---

## 📦 Prerequisites

1. ✅ n8n server sudah running (di server yang sama dengan Laravel)
2. ✅ WhatsApp Gateway sudah running (`whatsapp-server/server.js`)
3. ✅ Laravel aplikasi absensi sudah jalan

---

## 🚀 Step 1: Import Workflow ke n8n

### 1. Akses n8n UI
Buka browser: `https://n8n.dmcenter.my.id`

### 2. Import Workflow
1. Klik **"+" → Import from File**
2. Pilih file: `n8n-chatbot-walikelas-workflow.json`
3. Klik **"Import"**

### 3. Aktivasi Workflow
1. Workflow akan terbuka otomatis
2. Klik tombol **"Active"** (toggle ON)
3. Workflow sekarang listening di webhook

---

## 🔧 Step 2: Konfigurasi n8n Workflow

### Node yang Perlu Dikonfigurasi:

#### **A. Node "HTTP - Get Summary dari Laravel"**

**Update URL sesuai domain Anda:**
```
URL: https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{{ $json.body.from }}
Method: GET
```

**Jika butuh authentication (opsional):**
1. Klik node → **"Credentials"**
2. Pilih **"Header Auth"**
3. Tambahkan:
   - Header Name: `Authorization`
   - Header Value: `Bearer YOUR_API_TOKEN`

#### **B. Node "HTTP - Send Reply ke WA Gateway"**

**Pastikan URL sesuai port WA Gateway:**
```
URL: http://localhost:3001/reply
Method: POST
Body:
  - phone: {{ $json.phone }}
  - message: {{ $json.message }}
```

**Jika WA Gateway di port lain, ganti `3000` dengan port yang sesuai.**

---

## ⚙️ Step 3: Update WhatsApp Gateway

### File: `whatsapp-server/server.js`

**Tambahkan dependency axios (jika belum ada):**
```bash
cd whatsapp-server
npm install axios
```

**Tambahkan di bagian atas file (setelah requires lain):**
```javascript
const axios = require('axios');
```

**Update event handler `messages.upsert`:**

**SEBELUM:**
```javascript
sock.ev.on('messages.upsert', async ({ messages }) => {
    const msg = messages[0];
    if (!msg.key.fromMe && msg.message) {
        logger.info('Received message:', msg.message);
        // You can add auto-reply logic here if needed
    }
});
```

**SESUDAH:**
```javascript
sock.ev.on('messages.upsert', async ({ messages }) => {
    const msg = messages[0];
    if (!msg.key.fromMe && msg.message) {
        const from = msg.key.remoteJid.replace('@s.whatsapp.net', '');
        const text = msg.message.conversation || msg.message.extendedTextMessage?.text || '';
        
        logger.info(`Received message from ${from}: ${text}`);
        
        // Forward ke n8n untuk processing
        try {
            await axios.post('https://n8n.dmcenter.my.id/webhook/wa-chatbot', {
                from: from,
                message: text,
                timestamp: new Date()
            });
            logger.info('Message forwarded to n8n');
        } catch (error) {
            logger.error('Failed to forward message to n8n:', error.message);
        }
    }
});
```

**Tambahkan endpoint `/reply` (sebelum `app.listen`):**
```javascript
// Reply endpoint (dipanggil oleh n8n)
app.post('/reply', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                message: 'Phone and message are required'
            });
        }

        if (connectionState !== 'connected') {
            return res.status(503).json({
                success: false,
                message: 'WhatsApp not connected',
                status: connectionState
            });
        }

        const formattedPhone = formatPhoneNumber(phone);
        
        await sock.sendMessage(formattedPhone, { text: message });
        
        logger.info(`Reply sent to ${phone} via n8n`);
        
        res.json({
            success: true,
            message: 'Reply sent successfully',
            to: phone,
            timestamp: new Date().toISOString()
        });

    } catch (error) {
        logger.error('Failed to send reply:', error);
        res.status(500).json({
            success: false,
            message: 'Failed to send reply',
            error: error.message
        });
    }
});
```

**Restart WhatsApp Gateway:**
```bash
pm2 restart whatsapp-gateway-absensi
# atau
pm2 restart ecosystem.config.js
```

---

## 🔌 Step 4: Buat Laravel API Endpoint

### File: `routes/api.php`

**Tambahkan route:**
```php
Route::get('/chatbot/summary/{phone}', [App\Http\Controllers\ChatbotController::class, 'getSummary']);
```

### File: `app/Http/Controllers/ChatbotController.php`

**Buat controller baru:**
```bash
php artisan make:controller ChatbotController
```

**Isi controller:**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function getSummary($phone)
    {
        try {
            // Normalize phone number (remove 62 prefix if exists)
            $normalizedPhone = $phone;
            if (str_starts_with($phone, '62')) {
                $normalizedPhone = '0' . substr($phone, 2);
            }
            
            // Cari wali kelas berdasarkan nomor WA
            // Asumsi: ada field 'phone' di tabel users atau tabel wali_kelas
            $waliKelas = User::where('phone', $normalizedPhone)
                            ->orWhere('phone', $phone)
                            ->first();
            
            if (!$waliKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor Anda tidak terdaftar sebagai wali kelas. Silakan hubungi admin.',
                    'phone' => $phone
                ]);
            }
            
            // Cari kelas yang dipegang wali kelas ini
            // Asumsi: ada field 'wali_kelas_id' di tabel attendance_classes
            $kelas = AttendanceClass::where('wali_kelas_id', $waliKelas->id)->first();
            
            if (!$kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum ditugaskan sebagai wali kelas. Silakan hubungi admin.',
                    'phone' => $phone
                ]);
            }
            
            // Ambil data absensi hari ini
            $today = Carbon::today()->toDateString();
            
            // Ambil semua siswa di kelas
            $students = AttendanceStudent::where('class_id', $kelas->id)->get();
            $totalSiswa = $students->count();
            
            // Ambil record absensi hari ini
            $attendanceToday = AttendanceRecord::whereDate('created_at', $today)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
            
            // Hitung statistik
            $hadir = $attendanceToday->where('status', 'hadir')->count();
            $sakit = $attendanceToday->where('status', 'sakit')->count();
            $izin = $attendanceToday->where('status', 'izin')->count();
            $alpha = $totalSiswa - $attendanceToday->count(); // yang belum absen
            
            // List siswa yang tidak masuk (alpha)
            $studentIdsPresent = $attendanceToday->pluck('student_id');
            $studentsTidakHadir = $students->whereNotIn('id', $studentIdsPresent);
            
            $tidakHadirList = $studentsTidakHadir->map(function($siswa) {
                return [
                    'nis' => $siswa->nis,
                    'nama' => $siswa->name,
                ];
            })->values();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'wali_kelas_nama' => $waliKelas->name,
                    'kelas_nama' => $kelas->name,
                    'tanggal' => Carbon::parse($today)->isoFormat('DD MMMM YYYY'),
                    'total_siswa' => $totalSiswa,
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                    'tidak_hadir' => $tidakHadirList,
                    'nomor_wa' => $phone,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'phone' => $phone
            ], 500);
        }
    }
}
```

---

## 🧪 Step 5: Testing

### Test n8n Webhook

**Manual test via curl:**
```bash
curl -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot \
  -H "Content-Type: application/json" \
  -d '{
    "from": "6281234567890",
    "message": "help",
    "timestamp": "2026-08-12T10:00:00.000Z"
  }'
```

**Expected response:**
```json
{
  "success": true,
  "message": "Reply sent"
}
```

### 2. Test Laravel API

```bash
curl http://localhost:8000/api/chatbot/summary/6281234567890
```

**Expected response:**
```json
{
  "success": true,
  "data": {
    "wali_kelas_nama": "Bu Rina",
    "kelas_nama": "X AKL",
    "tanggal": "12 Agustus 2026",
    "total_siswa": 25,
    "hadir": 23,
    "sakit": 0,
    "izin": 0,
    "alpha": 2,
    "tidak_hadir": [
      {"nis": "12345", "nama": "Rian"},
      {"nis": "12346", "nama": "Siti"}
    ],
    "nomor_wa": "6281234567890"
  }
}
```

### 3. Test End-to-End via WhatsApp

1. **Kirim WA ke nomor gateway:** `help`
2. **Bot balas:** Menu perintah
3. **Kirim WA:** `ringkasan kehadiran hari ini`
4. **Bot balas:** Detail ringkasan absensi

---

## 📝 Perintah Chatbot yang Tersedia

| Perintah | Fungsi | Status |
|----------|--------|--------|
| `ringkasan kehadiran hari ini` | Lihat ringkasan absensi hari ini | ✅ Active |
| `ringkasan` | Shortcut untuk ringkasan hari ini | ✅ Active |
| `help` | Tampilkan menu perintah | ✅ Active |
| `statistik minggu ini` | Statistik mingguan | 🚧 Coming Soon |

---

## 🔧 Troubleshooting

### 1. n8n webhook tidak respond

**Cek:**
```bash
# Pastikan n8n running
curl http://localhost:5678/healthz

# Cek workflow status di n8n UI (pastikan Active = ON)
```

### 2. WhatsApp Gateway tidak forward message

**Cek log:**
```bash
pm2 logs whatsapp-gateway-absensi
```

**Pastikan axios installed:**
```bash
cd whatsapp-server
npm list axios
```

### 3. Laravel API error

**Cek log:**
```bash
tail -f storage/logs/laravel.log
```

**Test API langsung:**
```bash
curl http://localhost:8000/api/chatbot/summary/TEST_PHONE
```

### 4. Bot tidak balas

**Flow debugging:**
1. Cek log WA Gateway → Apakah pesan diterima?
2. Cek n8n execution history → Apakah workflow triggered?
3. Cek Laravel API → Apakah return data?
4. Cek n8n node "HTTP - Send Reply" → Apakah sukses?

---

## 🎨 Customization

### Ubah Format Pesan

Edit node **"Function - Format Pesan Ringkasan"** di n8n:

```javascript
// Contoh: Tambah emoji atau format lain
const message = `🏫 *ABSENSI ${summary.kelas_nama}*
📅 ${summary.tanggal}

✅ ${summary.hadir} Hadir
❌ ${summary.alpha} Alpha

// dst...
`;
```

### Tambah Keyword Baru

1. Buka n8n workflow
2. Edit node **"Switch - Parse Keyword"**
3. Tambah condition baru
4. Buat Function node untuk handle keyword baru
5. Connect ke "HTTP - Send Reply"

### Tambah Fitur Statistik

1. Buat Laravel API endpoint baru: `/api/chatbot/statistics/{phone}`
2. Edit node **"Function - Statistik (Coming Soon)"**
3. Ganti dengan HTTP request ke endpoint baru

---

## 📊 Monitoring

### n8n Execution History

1. Buka n8n UI
2. Klik **"Executions"** (sidebar kiri)
3. Lihat log setiap execution (success/failed)

### WhatsApp Gateway Logs

```bash
pm2 logs whatsapp-gateway-absensi --lines 50
```

### Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

---

## 🔐 Security Notes

### Rate Limiting

**Tambahkan di Laravel API untuk prevent spam:**

```php
// routes/api.php
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/chatbot/summary/{phone}', [ChatbotController::class, 'getSummary']);
});
```

Max 10 request per menit per IP.

### Authentication (Opsional)

**Jika mau tambah API token:**

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chatbot/summary/{phone}', [ChatbotController::class, 'getSummary']);
});
```

Update n8n node "HTTP - Get Summary" dengan Bearer token.

---

## 📁 File Structure

```
absensi/
├── whatsapp-server/
│   └── server.js                          # Updated dengan incoming message handler
├── app/Http/Controllers/
│   └── ChatbotController.php              # API endpoint untuk chatbot
├── routes/
│   └── api.php                            # Route chatbot
├── n8n-chatbot-walikelas-workflow.json    # n8n workflow template
└── N8N_CHATBOT_SETUP.md                   # Dokumentasi ini
```

---

## ✅ Checklist Setup

- [ ] n8n server running
- [ ] Import workflow ke n8n
- [ ] Aktivasi workflow di n8n
- [ ] Update URL di n8n nodes (Laravel API & WA Gateway)
- [ ] Install axios di whatsapp-server
- [ ] Update server.js dengan incoming message handler
- [ ] Tambah endpoint /reply di server.js
- [ ] Restart WA Gateway
- [ ] Buat ChatbotController.php di Laravel
- [ ] Tambah route /api/chatbot/summary/{phone}
- [ ] Test n8n webhook
- [ ] Test Laravel API
- [ ] Test end-to-end via WhatsApp
- [ ] Monitor logs untuk debugging

---

## 🚀 Next Steps

Setelah setup berhasil, Anda bisa:

1. ✅ Tambah fitur statistik mingguan
2. ✅ Tambah command untuk lihat siswa alpha terbanyak
3. ✅ Tambah scheduled notification (kirim ringkasan otomatis jam 14:00)
4. ✅ Tambah logging conversation ke database
5. ✅ Tambah analytics chatbot usage

---

**Last Updated:** 12 Agustus 2026  
**Author:** Kiro AI Assistant  
**Project:** SPMB - Absensi System (SMK PGRI BLORA)

