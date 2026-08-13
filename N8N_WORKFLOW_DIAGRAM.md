# 🔄 n8n Workflow Diagram - Chatbot Wali Kelas

## 📊 Visual Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     WALI KELAS                                  │
│                                                                 │
│  📱 Kirim WA: "ringkasan kehadiran hari ini"                   │
└────────────────────────┬────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│              WHATSAPP GATEWAY (server.js)                       │
│                                                                 │
│  sock.ev.on('messages.upsert') {                               │
│    const from = msg.key.remoteJid                              │
│    const text = msg.message.conversation                       │
│                                                                 │
│    // Forward ke n8n                                           │
│    axios.post('http://localhost:5678/webhook/wa-chatbot', {    │
│      from: from,                                               │
│      message: text                                             │
│    })                                                          │
│  }                                                             │
└────────────────────────┬────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                    n8n WORKFLOW                                 │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  1. WEBHOOK TRIGGER                                      │  │
│  │     URL: /webhook/wa-chatbot                             │  │
│  │     Terima: { from, message, timestamp }                 │  │
│  └──────────┬───────────────────────────────────────────────┘  │
│             ↓                                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  2. SWITCH - PARSE KEYWORD                               │  │
│  │                                                          │  │
│  │     Cek message.toLowerCase() contains:                  │  │
│  │     ├─ "ringkasan" → Route A                            │  │
│  │     ├─ "statistik" → Route B                            │  │
│  │     ├─ "help"      → Route C                            │  │
│  │     └─ (unknown)   → Route D                            │  │
│  └──────────┬───────────────────────────────────────────────┘  │
│             ↓                                                   │
│  ┌─────────────────────┬─────────────────┬──────────────────┐  │
│  │  Route A            │  Route B        │  Route C/D       │  │
│  ↓                     ↓                 ↓                    │
┌──────────────────┐  ┌──────────────┐  ┌──────────────────┐    │
│  3A. HTTP REQ    │  │  3B. FUNC    │  │  3C. FUNC        │    │
│  Get Summary     │  │  Statistik   │  │  Help/Unknown    │    │
│                  │  │  (Coming)    │  │                  │    │
│  GET Laravel:    │  │              │  │  Return:         │    │
│  /api/chatbot/   │  │  Return:     │  │  { phone, msg }  │    │
│  summary/{phone} │  │  { phone,    │  │                  │    │
│                  │  │    message } │  │                  │    │
│  Return JSON     │  │              │  │                  │    │
└────────┬─────────┘  └──────┬───────┘  └────────┬─────────┘    │
         ↓                   ↓                   ↓               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  4. FUNCTION - FORMAT PESAN                              │  │
│  │                                                          │  │
│  │     Build message string:                                │  │
│  │     - Header (Selamat siang Bu...)                      │  │
│  │     - Rekapitulasi (Hadir, Sakit, Izin, Alpha)          │  │
│  │     - List siswa tidak hadir                            │  │
│  │     - Footer                                            │  │
│  │                                                          │  │
│  │     Return: { phone, message }                           │  │
│  └──────────┬───────────────────────────────────────────────┘  │
│             ↓                                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  5. HTTP REQUEST - SEND REPLY                            │  │
│  │                                                          │  │
│  │     POST http://localhost:3001/reply                     │  │
│  │     Body: {                                              │  │
│  │       phone: {{ $json.phone }},                          │  │
│  │       message: {{ $json.message }}                       │  │
│  │     }                                                    │  │
│  └──────────┬───────────────────────────────────────────────┘  │
│             ↓                                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  6. RESPOND TO WEBHOOK                                   │  │
│  │     Return: { success: true }                            │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────┬────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│              WHATSAPP GATEWAY (server.js)                       │
│                                                                 │
│  app.post('/reply', async (req, res) => {                      │
│    const { phone, message } = req.body;                        │
│    const formattedPhone = formatPhoneNumber(phone);            │
│                                                                 │
│    await sock.sendMessage(formattedPhone, {                    │
│      text: message                                             │
│    });                                                         │
│                                                                 │
│    res.json({ success: true });                                │
│  })                                                            │
└────────────────────────┬────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                     WALI KELAS                                  │
│                                                                 │
│  📱 Terima WA:                                                 │
│  "Selamat siang Bu Rina 👋                                     │
│   📊 RINGKASAN ABSENSI HARI INI                                │
│   Kelas: X AKL                                                 │
│   ✅ Hadir: 23 siswa                                           │
│   ❌ Alpha: Rian, Siti"                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔀 Switch Logic Detail

```
┌──────────────────────────────────────────────────┐
│        SWITCH - PARSE KEYWORD                    │
│                                                  │
│  Input: $json.body.message (lowercase)           │
│                                                  │
│  IF contains "ringkasan"                         │
│    ├─→ Output: "ringkasan"                      │
│    └─→ Next: HTTP Request (Get Summary)         │
│                                                  │
│  ELSE IF contains "statistik"                    │
│    ├─→ Output: "statistik"                      │
│    └─→ Next: Function (Statistik Coming Soon)   │
│                                                  │
│  ELSE IF contains "help"                         │
│    ├─→ Output: "help"                           │
│    └─→ Next: Function (Help Message)            │
│                                                  │
│  ELSE (fallback)                                 │
│    ├─→ Output: "extra"                          │
│    └─→ Next: Function (Unknown Command)         │
└──────────────────────────────────────────────────┘
```

---

## 📦 Node Details

### Node 1: Webhook Trigger
```
Type: n8n-nodes-base.webhook
Path: /webhook/wa-chatbot
Method: POST
Response Mode: Using 'Respond to Webhook' node

Input Format:
{
  "from": "6281234567890",
  "message": "ringkasan kehadiran hari ini",
  "timestamp": "2026-08-12T10:00:00.000Z"
}
```

### Node 2: Switch - Parse Keyword
```
Type: n8n-nodes-base.switch
Rules:
  1. message contains "ringkasan" → Output: ringkasan
  2. message contains "statistik" → Output: statistik
  3. message contains "help"      → Output: help
  Fallback: extra (unknown command)
```

### Node 3A: HTTP Request - Get Summary
```
Type: n8n-nodes-base.httpRequest
Method: GET
URL: https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{{ $json.body.from }}
Authentication: Optional (Bearer Token)

Response:
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
    "tidak_hadir": [...]
  }
}
```

### Node 4: Function - Format Pesan
```
Type: n8n-nodes-base.code
Language: JavaScript

Logic:
- Parse data dari Laravel API
- Build message string dengan format WhatsApp
- Handle error jika API gagal
- Return { phone, message }
```

### Node 5: HTTP Request - Send Reply
```
Type: n8n-nodes-base.httpRequest
Method: POST
URL: http://localhost:3001/reply
Body:
{
  "phone": "{{ $json.phone }}",
  "message": "{{ $json.message }}"
}
```

### Node 6: Respond to Webhook
```
Type: n8n-nodes-base.respondToWebhook
Response: { "success": true, "message": "Reply sent" }
```

---

## 🎯 Example Messages

### Input: "ringkasan kehadiran hari ini"

**n8n Processing:**
```
1. Webhook receives: { from: "6281234567890", message: "ringkasan kehadiran hari ini" }
2. Switch matches: "ringkasan" → Route A
3. HTTP GET: /api/chatbot/summary/6281234567890
4. Laravel returns: { success: true, data: {...} }
5. Function formats message
6. HTTP POST: /reply with formatted message
7. WA Gateway sends message
```

**Output Message:**
```
Selamat siang Bu Rina 👋

📊 *RINGKASAN ABSENSI HARI INI*
Kelas: *X AKL*
Tanggal: 12 Agustus 2026

📈 *Rekapitulasi:*
✅ Hadir: *23* siswa
🤒 Sakit: *0* siswa
📝 Izin: *0* siswa
❌ Alpha: *2* siswa

👥 Total siswa: *25*

⚠️ *Siswa Tidak Hadir:*
1. Rian (12345)
2. Siti (12346)

---
_Sistem Absensi SMK PGRI Blora_
_Ketik "help" untuk perintah lainnya_
```

### Input: "help"

**Output Message:**
```
🤖 *MENU CHATBOT WALI KELAS*

Perintah yang tersedia:

1️⃣ *ringkasan kehadiran hari ini*
   → Lihat ringkasan absensi hari ini

2️⃣ *statistik minggu ini*
   → Lihat statistik absensi minggu ini
   _(Coming soon)_

3️⃣ *help*
   → Tampilkan menu ini

---
_Sistem Absensi SMK PGRI Blora_
_Powered by n8n + Laravel_
```

### Input: "halo bot"

**Output Message:**
```
❓ Maaf, saya tidak mengerti perintah Anda.

Ketik *help* untuk melihat daftar perintah yang tersedia.

---
_Sistem Absensi SMK PGRI Blora_
```

---

## 🔧 Configuration Matrix

| Component | Port | URL | Notes |
|-----------|------|-----|-------|
| n8n | 5678 | http://localhost:5678 | Workflow engine |
| n8n Webhook | 5678 | http://localhost:5678/webhook/wa-chatbot | Incoming from WA Gateway |
| WhatsApp Gateway ABSENSI | 3001 | http://localhost:3001 | Send/receive WA |
| WA Gateway Reply | 3001 | http://localhost:3001/reply | Reply endpoint for n8n |
| Laravel API | 80/443 | https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{phone} | Data source |

---

## 📈 Performance Considerations

### Latency Breakdown

```
Total Response Time: ~2-4 seconds

┌─────────────────────────────────────────────────┐
│  Wali Kelas send WA                     0ms    │
│    ↓                                            │
│  WA Gateway receive                   ~100ms   │
│    ↓                                            │
│  Forward to n8n webhook                ~50ms   │
│    ↓                                            │
│  n8n parse & route                     ~50ms   │
│    ↓                                            │
│  n8n → Laravel API                    ~200ms   │
│    ↓                                            │
│  Laravel query DB & process           ~300ms   │
│    ↓                                            │
│  Laravel → n8n response               ~100ms   │
│    ↓                                            │
│  n8n format message                    ~50ms   │
│    ↓                                            │
│  n8n → WA Gateway /reply              ~100ms   │
│    ↓                                            │
│  WA Gateway send message             ~1000ms   │
│    ↓                                            │
│  Wali Kelas receive WA              ~1000ms   │
└─────────────────────────────────────────────────┘

TOTAL: ~3 seconds (typical)
```

### Optimization Tips

1. **Cache Laravel Response** - Cache hasil query untuk 1 menit
2. **Database Indexing** - Index pada field yang sering di-query
3. **n8n Execution Mode** - Gunakan "main" mode untuk workflow sederhana
4. **WhatsApp Rate Limit** - Max 20 pesan/menit untuk avoid ban

---

## 🐛 Debugging Checklist

```
□ n8n workflow is Active (toggle ON)?
□ n8n webhook URL accessible? (test with curl)
□ WhatsApp Gateway running? (pm2 list)
□ WhatsApp Gateway has axios installed? (npm list axios)
□ server.js updated with incoming message handler?
□ /reply endpoint added to server.js?
□ Laravel API endpoint exists? (php artisan route:list | grep chatbot)
□ ChatbotController created?
□ Database has attendance data for today?
□ Wali kelas phone number registered in database?
□ All URLs in n8n nodes correct (domain, port)?
```

---

## 🚀 Future Enhancements

### Phase 2: Advanced Features

1. **Statistik Mingguan**
   - Command: "statistik minggu ini"
   - Response: Attendance rate, top students, etc.

2. **Query Siswa Spesifik**
   - Command: "absensi rian"
   - Response: Detail absensi siswa Rian

3. **Scheduled Reports**
   - Auto-send ringkasan jam 14:00 setiap hari
   - Pakai n8n Schedule Trigger

4. **Multi-Language Support**
   - Detect keyword "summary" (English) atau "ringkasan" (Indo)

5. **Voice Message Support**
   - Transcribe voice → text → process

---

**Created:** 12 Agustus 2026  
**Version:** 1.0  
**Author:** Kiro AI Assistant
