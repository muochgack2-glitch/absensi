# SPMB WhatsApp Gateway Server

WhatsApp Gateway menggunakan Baileys untuk mengirim notifikasi otomatis ke pendaftar SPMB.

## 📋 Requirements

- Node.js v18 atau lebih baru
- npm atau yarn
- Nomor WhatsApp untuk gateway (jangan gunakan nomor pribadi)

## 🚀 Installation

### 1. Install Dependencies

```bash
cd whatsapp-server
npm install
```

### 2. Configure Environment

Copy `.env.example` ke `.env` dan sesuaikan:

```bash
cp .env.example .env
```

Edit `.env`:
```env
PORT=3000
HOST=localhost
LOG_LEVEL=info
```

### 3. Start Server

**Development mode (with auto-reload):**
```bash
npm run dev
```

**Production mode:**
```bash
npm start
```

**Using PM2 (recommended for production):**
```bash
# Install PM2 globally
npm install -g pm2

# Start server
pm2 start server.js --name spmb-wa-gateway

# View logs
pm2 logs spmb-wa-gateway

# Stop server
pm2 stop spmb-wa-gateway

# Restart server
pm2 restart spmb-wa-gateway

# Auto-start on system reboot
pm2 startup
pm2 save
```

## 📱 Scan QR Code

1. Start server
2. QR code akan muncul di terminal
3. Atau akses: `http://localhost:3000/qr`
4. Scan dengan WhatsApp di HP:
   - Buka WhatsApp
   - Tap menu (3 titik) → Linked Devices
   - Tap "Link a Device"
   - Scan QR code

## 🔌 API Endpoints

### 1. Health Check
```bash
GET http://localhost:3000/
```

Response:
```json
{
  "success": true,
  "message": "SPMB WhatsApp Gateway Server",
  "version": "1.0.0",
  "status": "connected",
  "timestamp": "2026-05-30T15:30:00.000Z"
}
```

### 2. Get Status
```bash
GET http://localhost:3000/status
```

Response:
```json
{
  "success": true,
  "status": "connected",
  "qrAvailable": false,
  "reconnectAttempts": 0,
  "timestamp": "2026-05-30T15:30:00.000Z"
}
```

### 3. Get QR Code
```bash
GET http://localhost:3000/qr
```

Response:
```json
{
  "success": true,
  "qr": "data:image/png;base64,iVBORw0KGgoAAAANS...",
  "message": "Scan this QR code with WhatsApp"
}
```

### 4. Send Single Message
```bash
POST http://localhost:3000/send
Content-Type: application/json

{
  "phone": "081234567890",
  "message": "Halo, ini pesan dari SPMB"
}
```

Response:
```json
{
  "success": true,
  "message": "Message sent successfully",
  "to": "081234567890",
  "timestamp": "2026-05-30T15:30:00.000Z"
}
```

### 5. Send Bulk Messages
```bash
POST http://localhost:3000/send-bulk
Content-Type: application/json

{
  "messages": [
    {
      "phone": "081234567890",
      "message": "Halo Budi, pendaftaran Anda berhasil"
    },
    {
      "phone": "081234567891",
      "message": "Halo Ani, pendaftaran Anda berhasil"
    }
  ]
}
```

Response:
```json
{
  "success": true,
  "message": "Sent 2 messages, 0 failed",
  "total": 2,
  "successCount": 2,
  "failedCount": 0,
  "results": [
    {
      "phone": "081234567890",
      "success": true,
      "timestamp": "2026-05-30T15:30:00.000Z"
    },
    {
      "phone": "081234567891",
      "success": true,
      "timestamp": "2026-05-30T15:30:01.000Z"
    }
  ]
}
```

### 6. Logout
```bash
POST http://localhost:3000/logout
```

Response:
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

## 🧪 Testing

### Test dengan cURL:

```bash
# Check status
curl http://localhost:3000/status

# Send message
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"081234567890","message":"Test message"}'
```

### Test dengan Postman:

1. Import collection dari `postman_collection.json` (jika ada)
2. Atau buat request manual sesuai endpoint di atas

## 📝 Phone Number Format

Server akan otomatis format nomor telepon:
- `081234567890` → `62812345678890@s.whatsapp.net`
- `+6281234567890` → `62812345678890@s.whatsapp.net`
- `6281234567890` → `62812345678890@s.whatsapp.net`

## 🔧 Troubleshooting

### QR Code tidak muncul
- Pastikan server running
- Cek logs: `pm2 logs spmb-wa-gateway`
- Restart server: `pm2 restart spmb-wa-gateway`

### Connection closed
- Cek koneksi internet
- Pastikan WhatsApp di HP tidak logout
- Server akan auto-reconnect (max 5 attempts)

### Message failed to send
- Cek status koneksi: `GET /status`
- Pastikan nomor tujuan valid
- Cek format nomor telepon

### Session expired
- Logout: `POST /logout`
- Restart server
- Scan QR code lagi

## 🔒 Security Notes

1. **Jangan expose port 3000 ke public** - Hanya untuk localhost/internal network
2. **Gunakan nomor khusus** - Jangan pakai nomor pribadi
3. **Rate limiting** - Ada delay 1 detik antar pesan untuk bulk send
4. **Session files** - Jangan commit folder session ke git (sudah di .gitignore)

## 📚 Documentation

- [Baileys Documentation](https://github.com/WhiskeySockets/Baileys)
- [Express.js Documentation](https://expressjs.com/)
- [PM2 Documentation](https://pm2.keymetrics.io/)

## ✨ Auto-Reconnect After Logout

**Version 1.1.0** - Fitur baru: Auto-generate QR code setelah logout

### Cara Kerja:
1. Logout dari dashboard admin → Server otomatis disconnect
2. Session folder dihapus otomatis
3. Server auto-reconnect dalam 3-5 detik
4. QR code baru di-generate otomatis
5. Dashboard auto-refresh dan tampilkan QR (tidak perlu restart PM2!)

### Testing:
```bash
# Linux/Mac
./test-logout.sh

# Windows
test-logout.bat

# Manual test
curl -X POST http://localhost:3000/logout
# Wait 5-10 seconds
curl http://localhost:3000/status
# Should show status: "qr" and qrAvailable: true
```

### Troubleshooting Auto-Reconnect:
Jika QR tidak muncul setelah logout:
```bash
# Check logs
pm2 logs spmb-wa-gateway --lines 50

# Should see:
# [INFO] Logout requested - preparing to disconnect...
# [INFO] Session folder deleted successfully
# [INFO] Manual logout detected - resetting reconnect counter
# [INFO] Reconnecting... Attempt 1/5
# [INFO] QR Code generated, scan with WhatsApp

# If stuck, restart PM2
pm2 restart spmb-wa-gateway
```

Lihat dokumentasi lengkap: `WHATSAPP_AUTO_RECONNECT_FIX.md`

## 🐛 Known Issues

1. **WhatsApp Web API changes** - Baileys menggunakan unofficial API, bisa berubah sewaktu-waktu
2. **Rate limiting** - WhatsApp bisa ban nomor jika spam
3. **Session persistence** - Kadang perlu scan ulang QR code (sudah ada auto-reconnect di v1.1.0)

## 📞 Support

Jika ada masalah, cek:
1. Server logs: `pm2 logs spmb-wa-gateway`
2. Laravel logs: `storage/logs/laravel.log`
3. Browser console (jika dari admin panel)
