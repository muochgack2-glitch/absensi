# 🔄 Restart WhatsApp Gateway

## Cara Restart Gateway

### Option 1: Manual Restart (Recommended)
```bash
# 1. Stop current gateway (Ctrl+C di terminal yang running)
# 2. Start gateway lagi
cd whatsapp-server
node server.js
```

### Option 2: Using PM2 (if installed)
```bash
pm2 restart whatsapp-gateway
# atau
pm2 restart all
```

### Option 3: Kill & Restart
```bash
# Windows
taskkill /F /IM node.exe
cd whatsapp-server
node server.js

# Linux
pkill node
cd whatsapp-server
node server.js
```

## Verify Gateway Running

### Check Status
```bash
curl http://localhost:3001/status
```

Expected response:
```json
{
  "success": true,
  "status": "connected",
  "qrAvailable": false,
  "reconnectAttempts": 0,
  "timestamp": "2026-08-10T..."
}
```

### Test Send Media Endpoint
```bash
curl -X POST http://localhost:3001/send-media \
  -F "phone=085216343400" \
  -F "caption=Test foto dari gateway" \
  -F "media=@C:\path\to\test-image.jpg"
```

Expected response:
```json
{
  "success": true,
  "message": "Media sent successfully",
  "to": "085216343400",
  "mediaType": "image",
  "messageId": "3EB0...",
  "timestamp": "2026-08-10T..."
}
```

## After Restart

1. ✅ Gateway supports `/send-media` endpoint
2. ✅ Laravel will automatically use it when photo enabled
3. ✅ Test with QR scan + photo capture

## Troubleshooting

### Gateway won't start?
```bash
cd whatsapp-server
npm install
node server.js
```

### Port already in use?
Change port in `.env` or kill process:
```bash
# Windows
netstat -ano | findstr :3001
taskkill /PID [PID] /F

# Linux
lsof -ti:3001 | xargs kill -9
```

### Missing multer package?
```bash
cd whatsapp-server
npm install multer
```
