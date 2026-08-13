# Testing Fully Dynamic n8n Chatbot Workflow

## Status
- ✅ File `n8n-chatbot-FULLY-DYNAMIC-v2.json` sudah di pull
- ✅ Test script `test-wa-chatbot-dynamic.sh` sudah tersedia
- ⏳ **PERLU DIIMPORT KE N8N DULU**

## Langkah Import Workflow ke n8n

### 1. Buka n8n Dashboard
```
https://n8n.dmcenter.my.id
```

### 2. Import Workflow
- Klik menu **Workflows** (sidebar kiri)
- Klik tombol **Import from File** atau **+ New Workflow** → **Import**
- Pilih file: `/www/wwwroot/absensi/n8n-chatbot-FULLY-DYNAMIC-v2.json`
- Klik **Import**

### 3. Aktivkan Workflow
- Pastikan workflow terbuka di editor
- **PENTING**: Pastikan workflow dalam mode **Production** (bukan Test)
  - Lihat di kanan atas, ada toggle "Test" vs "Production"
  - Set ke **Production**
- Toggle switch **Active** di kanan atas menjadi **ON** (warna hijau)
- Webhook path akan aktif: `/webhook/wa-chatbot-dynamic`

### 4. Verifikasi Workflow Settings
Pastikan node-node ini sudah benar:

**Webhook Node:**
- Path: `wa-chatbot-dynamic`
- Method: POST
- Response Mode: `responseNode` ✅

**Parse Input Node:**
- Mengambil phone dari: `{{ $json.body.from.split('@')[0] }}`
- Mengambil command dari: `{{ $json.body.message.toLowerCase() }}`

**Get Summary API Node:**
- URL: `https://absensi.smkpgriblora.sch.id/api/chatbot/summary/{{ $json.phone }}`

**Send to WhatsApp Node:**
- Method: POST
- URL: `https://absensi.smkpgriblora.sch.id/wa-reply`
- Body Parameters:
  - phone: `{{ $json.phone }}`
  - message: `{{ $json.message }}`

## Testing Setelah Import

### Test 1: Manual curl dengan verbose output
```bash
curl -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot-dynamic \
-H "Content-Type: application/json" \
-d '{"body":{"from":"85216343400@lid","message":"help"}}' \
-v
```

**Expected Response:**
```json
{"success":true,"phone":"85216343400"}
```

Dan WhatsApp harus menerima pesan help menu.

### Test 2: Test semua commands
```bash
cd /www/wwwroot/absensi
bash test-wa-chatbot-dynamic.sh
```

### Test 3: Cek n8n execution logs
- Buka n8n dashboard
- Klik menu **Executions** (sidebar kiri)
- Lihat executions untuk workflow "WhatsApp Chatbot - Fully Dynamic v2"
- Pastikan status **Success** (hijau)
- Jika error, klik execution untuk lihat detail error di node mana

## Troubleshooting

### Jika curl tidak return response:
1. **Workflow belum diimport** → Import dulu
2. **Workflow tidak aktif** → Toggle Active ON
3. **Mode masih Test** → Switch ke Production mode
4. **Path webhook salah** → Pastikan path: `wa-chatbot-dynamic`

### Jika return error "Workflow not found":
- Webhook path salah atau workflow belum aktif di Production mode

### Jika return error "Error in workflow":
- Buka Executions di n8n dashboard untuk lihat detail error
- Kemungkinan:
  - Expression syntax error
  - HTTP Request timeout
  - Laravel API error

### Jika WhatsApp tidak menerima pesan:
- Cek n8n execution: apakah sampai ke node "Send to WhatsApp"?
- Cek WhatsApp Gateway status: `curl http://localhost:3001/status`
- Cek WhatsApp Gateway logs: `pm2 logs whatsapp-absensi`

## Perbandingan Workflows

| Feature | v2 (Hardcoded) | Fully Dynamic v2 |
|---------|----------------|------------------|
| Webhook Path | `/webhook/wa-chatbot-v2` | `/webhook/wa-chatbot-dynamic` |
| Phone Number | Hardcoded `6285216343400` | Dynamic dari input |
| Message Content | Dynamic (expressions) | Dynamic (expressions) |
| Status | ✅ WORKING | ⏳ NEEDS TESTING |

## Next Steps Setelah Test Sukses

1. **Update whatsapp-server/server.js**
   - Ubah webhook path dari `/webhook/wa-chatbot-v2` ke `/webhook/wa-chatbot-dynamic`
   - Line yang perlu diubah: sekitar line 280-284

2. **Restart WhatsApp Gateway**
   ```bash
   pm2 restart whatsapp-absensi
   ```

3. **Test end-to-end via WhatsApp**
   - Kirim pesan "help" dari WhatsApp
   - Kirim pesan "ringkasan"
   - Pastikan bot membalas dengan benar

4. **Deactivate workflow lama**
   - Buka workflow "WhatsApp Chatbot - Hardcoded Multi HTTP" 
   - Toggle Active OFF
   - Atau delete jika tidak perlu backup

## Monitoring Production

### Cek n8n logs:
```bash
docker logs n8n_app --tail 50 -f
```

### Cek WhatsApp Gateway logs:
```bash
pm2 logs whatsapp-absensi
```

### Cek Laravel logs:
```bash
tail -f /www/wwwroot/absensi/storage/logs/laravel.log
```

### Health check endpoints:
```bash
# WhatsApp Gateway
curl http://localhost:3001/status

# n8n
curl https://n8n.dmcenter.my.id/healthz

# Laravel API
curl https://absensi.smkpgriblora.sch.id/api/chatbot/summary/6285216343400
```
