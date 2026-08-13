# 🚀 Deployment Guide - n8n WhatsApp Chatbot

**Sistem Absensi SMK PGRI Blora**  
**Date:** 12 Agustus 2026  
**Version:** 1.0

---

## ✅ Pre-Deployment Checklist

Pastikan hal-hal berikut sudah ready:

- [ ] Server sudah login sebagai root atau user dengan sudo access
- [ ] Laravel app running di `/www/wwwroot/absensi`
- [ ] WhatsApp Gateway running di `/www/wwwroot/absensi/whatsapp-server`
- [ ] n8n running di `https://n8n.dmcenter.my.id`
- [ ] n8n workflow JSON file: `n8n-chatbot-walikelas-workflow-v2.json`
- [ ] Database credentials ready
- [ ] PM2 installed untuk manage WhatsApp Gateway

---

## 📦 Files yang Sudah Dibuat

```
✅ whatsapp-server/server.js (updated)
✅ app/Http/Controllers/ChatbotController.php (new)
✅ routes/api.php (updated)
✅ database/migrations/2026_08_12_000001_add_phone_to_users_table.php (new)
✅ app/Models/User.php (updated - phone in fillable)
✅ setup-chatbot.sh (deployment script)
✅ n8n-chatbot-walikelas-workflow-v2.json (import ke n8n)
```

---

## 🔧 Step-by-Step Deployment

### **STEP 1: Upload Files ke Server**

```bash
# Via SSH
cd /www/wwwroot/absensi

# Pastikan files sudah ter-upload:
ls -la app/Http/Controllers/ChatbotController.php
ls -la database/migrations/2026_08_12_000001_add_phone_to_users_table.php
ls -la setup-chatbot.sh
ls -la whatsapp-server/server.js
```

### **STEP 2: Run Setup Script (Otomatis)**

```bash
cd /www/wwwroot/absensi

# Make script executable
chmod +x setup-chatbot.sh

# Run setup
./setup-chatbot.sh
```

**Script akan otomatis:**
1. Install axios di WhatsApp Gateway
2. Run migration (add phone field)
3. Verify migration
4. Clear Laravel cache
5. Restart WhatsApp Gateway

---

### **STEP 3: Manual Steps (Jika Script Error)**

#### **3.1 Install axios**

```bash
cd /www/wwwroot/absensi/whatsapp-server
npm install axios
```

#### **3.2 Run Migration**

```bash
cd /www/wwwroot/absensi
php artisan migrate --force
```

Verify:
```bash
php artisan tinker

# Di dalam tinker:
Schema::hasColumn('users', 'phone')
// Should return: true

exit
```

#### **3.3 Clear Cache**

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

#### **3.4 Restart Gateway**

```bash
pm2 restart whatsapp-gateway-absensi

# Check status
pm2 status
pm2 logs whatsapp-gateway-absensi --lines 20
```

---

### **STEP 4: Import n8n Workflow**

1. **Open n8n:**
   ```
   https://n8n.dmcenter.my.id
   ```

2. **Login ke n8n**

3. **Import Workflow:**
   - Klik **"+"** (New Workflow) atau **"Import from File"**
   - Upload file: `n8n-chatbot-walikelas-workflow-v2.json`
   - Klik **"Import"**

4. **Verify Nodes:**
   
   **Node: "HTTP - Get Summary dari Laravel"**
   - Method: `GET`
   - URL: `https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{{ $json.body.from }}`
   
   **Node: "HTTP - Send Reply ke WA Gateway"**
   - Method: `POST`
   - URL: `http://localhost:3001/reply`
   - Body:
     ```json
     {
       "phone": "{{ $json.body.from }}",
       "message": "{{ $json.formatted_message }}"
     }
     ```

5. **Activate Workflow:**
   - Toggle **"Active"** ON
   - Pastikan status: 🟢 Active

6. **Save Workflow**

---

### **STEP 5: Populate Data Wali Kelas**

Tambahkan nomor WhatsApp untuk wali kelas existing:

```bash
cd /www/wwwroot/absensi
php artisan tinker
```

**Dalam tinker:**

```php
// Contoh: Update Bu Rina
$user = User::where('email', 'rina@smkpgriblora.sch.id')->first();
$user->phone = '6285216343400'; // Format: 62xxx (WITHOUT +)
$user->save();

// Verify
echo "Updated: " . $user->name . " - Phone: " . $user->phone;

// Update lebih banyak wali kelas
$user2 = User::where('email', 'budi@smkpgriblora.sch.id')->first();
$user2->phone = '6281234567890';
$user2->save();

// List all wali kelas
User::where('role', 'wali_kelas')->get(['id', 'name', 'email', 'phone', 'kelas_id']);

exit
```

**PENTING:** Format nomor WA harus `62xxx` (TANPA `+`).

---

## 🧪 Testing

### **Test 1: n8n Webhook**

```bash
curl -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot \
  -H "Content-Type: application/json" \
  -d '{"body":{"from":"6285216343400","message":"help"}}'
```

**Expected:** HTTP 200 OK

---

### **Test 2: Laravel API**

```bash
curl https://absensi.smkpgriblora.sch.id/api/chatbot/summary/6285216343400
```

**Expected:**
```json
{
  "success": true,
  "data": {
    "wali_kelas_nama": "Bu Rina",
    "kelas_nama": "X AKL",
    "tanggal": "12 Agustus 2026",
    "total_siswa": 30,
    "hadir": 25,
    "sakit": 2,
    "izin": 1,
    "alpha": 2,
    "tidak_hadir": [...]
  }
}
```

---

### **Test 3: WhatsApp Gateway Reply Endpoint**

```bash
curl -X POST http://localhost:3001/reply \
  -H "Content-Type: application/json" \
  -d '{"phone":"6285216343400","message":"Test reply dari n8n"}'
```

**Expected:**
```json
{
  "success": true,
  "message": "Reply sent successfully",
  "to": "6285216343400"
}
```

---

### **Test 4: End-to-End via WhatsApp**

1. **Kirim pesan ke nomor WhatsApp Gateway:**
   ```
   help
   ```

2. **Expected reply:**
   ```
   🤖 Menu Chatbot Absensi

   Perintah yang tersedia:
   • ringkasan kehadiran hari ini
   • ringkasan
   • statistik
   • help

   Ketik salah satu perintah di atas.
   ```

3. **Kirim pesan:**
   ```
   ringkasan kehadiran hari ini
   ```

4. **Expected reply:**
   ```
   📊 Ringkasan Kehadiran

   👤 Wali Kelas: Bu Rina
   🏫 Kelas: X AKL
   📅 Tanggal: 12 Agustus 2026

   👥 Total Siswa: 30
   ✅ Hadir: 25
   🤒 Sakit: 2
   📝 Izin: 1
   ❌ Alpha: 2

   Siswa yang tidak hadir:
   • Rian (NIS: 12345)
   • Budi (NIS: 12346)
   ```

---

## 🐛 Troubleshooting

### **Problem 1: n8n webhook tidak respond**

**Symptoms:**
- Curl test ke webhook timeout
- n8n execution history kosong

**Solution:**
```bash
# Check n8n running
curl https://n8n.dmcenter.my.id/healthz

# Check n8n logs (via Proxmox atau systemd)
journalctl -u n8n -f

# Restart n8n
systemctl restart n8n

# Verify workflow Active di n8n UI
```

---

### **Problem 2: Laravel API error 404**

**Symptoms:**
```json
{"message":"Not Found"}
```

**Solution:**
```bash
cd /www/wwwroot/absensi

# Clear route cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Verify route exists
php artisan route:list | grep chatbot
# Should show: GET api/chatbot/summary/{phone}

# Test again
curl https://absensi.smkpgriblora.sch.id/api/chatbot/summary/TEST
```

---

### **Problem 3: Laravel API error 500**

**Symptoms:**
```json
{"success":false,"message":"Terjadi kesalahan sistem"}
```

**Solution:**
```bash
# Check Laravel logs
tail -f /www/wwwroot/absensi/storage/logs/laravel.log

# Common issues:
# - Database connection error
# - Missing AttendanceClass/AttendanceStudent models
# - Carbon timezone issue

# Test database connection
php artisan tinker
User::count();
exit
```

---

### **Problem 4: WhatsApp Gateway tidak forward ke n8n**

**Symptoms:**
- Kirim WA, tidak ada respon
- PM2 logs tidak ada error

**Solution:**
```bash
# Check PM2 logs
pm2 logs whatsapp-gateway-absensi --lines 50

# Should see:
# "📨 Received from 628xxx: help"
# "✅ Message forwarded to n8n chatbot"

# If NOT forwarding:
cd /www/wwwroot/absensi/whatsapp-server

# Check axios installed
npm list axios

# If not installed:
npm install axios

# Restart gateway
pm2 restart whatsapp-gateway-absensi

# Test again
```

---

### **Problem 5: WhatsApp Gateway tidak kirim reply**

**Symptoms:**
- n8n workflow execute sampai habis (sukses)
- Tapi tidak ada WA reply

**Solution:**
```bash
# Check PM2 logs
pm2 logs whatsapp-gateway-absensi --lines 50

# Should see:
# "✅ Reply sent to 628xxx (from n8n chatbot)"

# Common issue: WA not connected
curl http://localhost:3001/status

# If status != "connected":
# 1. Scan QR di UI WhatsApp Gateway
# 2. Atau restart:
pm2 restart whatsapp-gateway-absensi
```

---

### **Problem 6: Migration error "phone already exists"**

**Symptoms:**
```
SQLSTATE[42S21]: Column already exists: phone
```

**Solution:**
```bash
# Migration sudah pernah dijalankan
# Check table structure:
php artisan tinker

Schema::hasColumn('users', 'phone')
// If true, migration already done

# OR manually check DB:
mysql -u absensi_db -p absensi_db
DESCRIBE users;
```

---

## 📊 Monitoring

### **Check System Status**

```bash
# 1. n8n Status
curl https://n8n.dmcenter.my.id/healthz

# 2. WhatsApp Gateway Status
curl http://localhost:3001/status

# 3. Laravel Status
curl https://absensi.smkpgriblora.sch.id/api/chatbot/summary/TEST

# 4. PM2 Status
pm2 status

# 5. n8n Workflow Executions
# Via UI: https://n8n.dmcenter.my.id -> Executions tab
```

### **View Logs**

```bash
# WhatsApp Gateway logs
pm2 logs whatsapp-gateway-absensi --lines 100

# Laravel logs
tail -f /www/wwwroot/absensi/storage/logs/laravel.log

# n8n logs (if using systemd)
journalctl -u n8n -f --lines 100
```

---

## 🔐 Security Notes

1. **API Endpoint Public:** `/api/chatbot/summary/{phone}` adalah public endpoint
   - Pertimbangkan tambah authentication (Bearer token)
   - Atau IP whitelist (hanya n8n server yang bisa akses)

2. **Data Privacy:** Nomor WA wali kelas adalah data sensitif
   - Pastikan HTTPS enabled
   - Jangan log nomor WA di plain text

3. **Rate Limiting:** Pertimbangkan tambah rate limiting untuk prevent abuse

---

## 📈 Future Improvements

- [ ] Add authentication untuk API endpoint
- [ ] Add rate limiting
- [ ] Add statistics command ("statistik")
- [ ] Add scheduled daily notifications (8 AM reminder)
- [ ] Add admin commands (broadcast message)
- [ ] Add logging/analytics (track chatbot usage)
- [ ] Add multi-language support
- [ ] Add voice note support
- [ ] Add image/PDF report generation

---

## 📞 Support

Jika ada masalah:

1. **Check logs first:**
   - PM2 logs: `pm2 logs whatsapp-gateway-absensi`
   - Laravel logs: `tail -f storage/logs/laravel.log`
   - n8n execution history (via UI)

2. **Run tests:**
   - Test webhook
   - Test Laravel API
   - Test WA Gateway reply endpoint

3. **Restart services:**
   ```bash
   pm2 restart whatsapp-gateway-absensi
   systemctl restart n8n
   ```

---

## ✅ Deployment Checklist

- [ ] Upload all files ke server
- [ ] Run setup-chatbot.sh
- [ ] Install axios di whatsapp-server
- [ ] Run migration (add phone field)
- [ ] Clear Laravel cache
- [ ] Restart WhatsApp Gateway
- [ ] Import workflow ke n8n
- [ ] Activate workflow
- [ ] Update nomor WA wali kelas
- [ ] Test webhook
- [ ] Test Laravel API
- [ ] Test reply endpoint
- [ ] Test end-to-end via WhatsApp
- [ ] Monitor logs
- [ ] Document any issues

---

**Status:** ✅ Ready to Deploy  
**Estimated Time:** 20-30 minutes  
**Difficulty:** Medium

**Last Updated:** 12 Agustus 2026  
**Created By:** Kiro AI Assistant
