# 🚀 n8n Chatbot - Quick Reference Card

## 📋 Import Workflow (5 menit)

```bash
# 1. Akses n8n
http://localhost:5678

# 2. Import workflow
+ → Import from File → n8n-chatbot-walikelas-workflow.json

# 3. Aktivasi
Toggle "Active" ON
```

---

## ⚙️ Update URLs di n8n (2 node)

### Node: "HTTP - Get Summary dari Laravel"
```
URL: https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{{ $json.body.from }}
```

### Node: "HTTP - Send Reply ke WA Gateway"
```
URL: http://localhost:3001/reply
```

---

## 🔧 Update WhatsApp Gateway (10 menit)

### 1. Install axios
```bash
cd whatsapp-server
npm install axios
```

### 2. Edit server.js

**A. Tambah di bagian atas (setelah requires):**
```javascript
const axios = require('axios');
```

**B. Update event handler (replace existing):**
```javascript
sock.ev.on('messages.upsert', async ({ messages }) => {
    const msg = messages[0];
    if (!msg.key.fromMe && msg.message) {
        const from = msg.key.remoteJid.replace('@s.whatsapp.net', '');
        const text = msg.message.conversation || msg.message.extendedTextMessage?.text || '';
        
        logger.info(`Received message from ${from}: ${text}`);
        
        try {
            await axios.post('http://localhost:5678/webhook/wa-chatbot', {
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

**C. Tambah endpoint /reply (sebelum app.listen):**
```javascript
app.post('/reply', async (req, res) => {
    try {
        const { phone, message } = req.body;
        if (!phone || !message) {
            return res.status(400).json({ success: false, message: 'Phone and message required' });
        }
        if (connectionState !== 'connected') {
            return res.status(503).json({ success: false, message: 'WhatsApp not connected' });
        }
        
        const formattedPhone = formatPhoneNumber(phone);
        await sock.sendMessage(formattedPhone, { text: message });
        
        logger.info(`Reply sent to ${phone} via n8n`);
        res.json({ success: true, message: 'Reply sent', to: phone });
    } catch (error) {
        logger.error('Failed to send reply:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});
```

### 3. Restart Gateway
```bash
pm2 restart whatsapp-gateway-absensi
```

---

## 🐘 Laravel API Endpoint (10 menit)

### 1. Buat Controller
```bash
php artisan make:controller ChatbotController
```

### 2. Edit routes/api.php
```php
Route::get('/chatbot/summary/{phone}', [App\Http\Controllers\ChatbotController::class, 'getSummary']);
```

### 3. Controller Code

**File:** `app/Http/Controllers/ChatbotController.php`

```php
<?php

namespace App\Http\Controllers;

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
            // Normalize phone
            $normalizedPhone = $phone;
            if (str_starts_with($phone, '62')) {
                $normalizedPhone = '0' . substr($phone, 2);
            }
            
            // Find wali kelas
            $waliKelas = User::where('phone', $normalizedPhone)
                            ->orWhere('phone', $phone)
                            ->first();
            
            if (!$waliKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor tidak terdaftar sebagai wali kelas.'
                ]);
            }
            
            // Find class
            $kelas = AttendanceClass::where('wali_kelas_id', $waliKelas->id)->first();
            if (!$kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum ditugaskan sebagai wali kelas.'
                ]);
            }
            
            // Today's data
            $today = Carbon::today()->toDateString();
            $students = AttendanceStudent::where('class_id', $kelas->id)->get();
            $totalSiswa = $students->count();
            
            $attendanceToday = AttendanceRecord::whereDate('created_at', $today)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();
            
            $hadir = $attendanceToday->where('status', 'hadir')->count();
            $sakit = $attendanceToday->where('status', 'sakit')->count();
            $izin = $attendanceToday->where('status', 'izin')->count();
            $alpha = $totalSiswa - $attendanceToday->count();
            
            $studentIdsPresent = $attendanceToday->pluck('student_id');
            $tidakHadir = $students->whereNotIn('id', $studentIdsPresent)
                ->map(fn($s) => ['nis' => $s->nis, 'nama' => $s->name])
                ->values();
            
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
                    'tidak_hadir' => $tidakHadir,
                    'nomor_wa' => $phone,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
```

---

## 🧪 Testing Commands

### Test n8n Webhook
```bash
curl -X POST http://localhost:5678/webhook/wa-chatbot \
  -H "Content-Type: application/json" \
  -d '{"from":"6281234567890","message":"help"}'
```

### Test Laravel API
```bash
curl http://localhost:8000/api/chatbot/summary/6281234567890
```

### Test via WhatsApp
```
Kirim WA ke nomor gateway: "help"
```

---

## 📱 Available Commands

| Command | Response |
|---------|----------|
| `ringkasan kehadiran hari ini` | Detail absensi hari ini |
| `ringkasan` | Shortcut untuk ringkasan |
| `help` | Menu perintah |
| `statistik` | Coming soon |

---

## 🐛 Quick Troubleshooting

### Webhook tidak respond
```bash
# Cek n8n running
curl http://localhost:5678/healthz

# Cek workflow Active di n8n UI
```

### WA Gateway tidak forward
```bash
# Cek log
pm2 logs whatsapp-gateway-absensi --lines 20

# Pastikan axios installed
cd whatsapp-server && npm list axios
```

### Laravel API error
```bash
# Test API
curl http://localhost:8000/api/chatbot/summary/TEST

# Cek log
tail -f storage/logs/laravel.log
```

---

## 📁 Files Created

```
✅ n8n-chatbot-walikelas-workflow.json    # Import ke n8n
✅ N8N_CHATBOT_SETUP.md                   # Full documentation
✅ N8N_WORKFLOW_DIAGRAM.md                # Visual flow
✅ N8N_QUICK_REFERENCE.md                 # This file
```

---

## ✅ Setup Checklist

- [ ] Import workflow ke n8n
- [ ] Aktivasi workflow (toggle ON)
- [ ] Update 2 URLs di n8n nodes
- [ ] Install axios di whatsapp-server
- [ ] Update server.js (3 changes)
- [ ] Restart WA Gateway
- [ ] Buat ChatbotController.php
- [ ] Tambah route /api/chatbot/summary
- [ ] Test with curl
- [ ] Test with WhatsApp

---

## 🎯 Next Steps

Setelah setup:
1. Monitor n8n execution history
2. Cek logs (pm2 logs / Laravel log)
3. Tambah fitur statistik
4. Tambah scheduled notification

---

**Estimated Setup Time:** 30 menit  
**Difficulty:** Medium  
**Required Skills:** Basic Linux, basic PHP, basic n8n
