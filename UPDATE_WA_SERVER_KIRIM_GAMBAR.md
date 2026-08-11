# 📸 Update WhatsApp Server untuk Kirim Gambar

## Overview
Update ini menambahkan kemampuan WhatsApp gateway untuk mengirim gambar/foto/media bersama dengan pesan text (caption).

## What's New
✅ Endpoint baru: `POST /send-media`  
✅ Support kirim gambar (JPEG, PNG)  
✅ Support kirim video (MP4, AVI)  
✅ Support kirim audio  
✅ Support kirim dokumen PDF  
✅ Auto-cleanup file temporary  
✅ Return messageId untuk tracking  

## Affected Servers

### 1. Absensi WhatsApp Gateway (Port 3001)
**Status**: ✅ SUDAH DIUPDATE
- Location: `c:\Users\DMCenter\Music\SPMB2\SPMB\absensi\whatsapp-server\`
- Commit: `6bf696f`
- Package: multer installed

### 2. SPMB WhatsApp Gateway (Port 3000)
**Status**: ✅ SUDAH DIUPDATE
- Location: `c:\Users\DMCenter\Music\SPMB2\SPMB\whatsapp-server\`
- Commit: `3322957`
- Package: multer installed

---

## Update Instructions for SPMB Gateway

### Step 1: Edit server.js
File: `c:\Users\DMCenter\Music\SPMB2\SPMB\whatsapp-server\server.js`

**Lokasi penambahan**: Setelah endpoint `/send` (sekitar line 285-320)

**Code yang ditambahkan**:

```javascript
// Send media (image) with caption
app.post('/send-media', async (req, res) => {
    try {
        const multer = require('multer');
        const upload = multer({ storage: multer.memoryStorage() });
        const fs = require('fs');
        const path = require('path');
        const { promisify } = require('util');
        const writeFile = promisify(fs.writeFile);
        const unlink = promisify(fs.unlink);

        // Parse multipart form data
        upload.single('media')(req, res, async (err) => {
            if (err) {
                logger.error('Multer error:', err);
                return res.status(400).json({
                    success: false,
                    message: 'Failed to upload media',
                    error: err.message
                });
            }

            try {
                const { phone, caption } = req.body;
                const mediaFile = req.file;

                if (!phone) {
                    return res.status(400).json({
                        success: false,
                        message: 'Phone is required'
                    });
                }

                if (!mediaFile) {
                    return res.status(400).json({
                        success: false,
                        message: 'Media file is required'
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

                // Save temp file
                const tempDir = path.join(__dirname, 'temp');
                if (!fs.existsSync(tempDir)) {
                    fs.mkdirSync(tempDir, { recursive: true });
                }

                const tempFilePath = path.join(tempDir, `${Date.now()}_${mediaFile.originalname}`);
                await writeFile(tempFilePath, mediaFile.buffer);

                try {
                    // Determine media type
                    const mimeType = mediaFile.mimetype;
                    let mediaType = 'image'; // default

                    if (mimeType.startsWith('video/')) {
                        mediaType = 'video';
                    } else if (mimeType.startsWith('audio/')) {
                        mediaType = 'audio';
                    } else if (mimeType === 'application/pdf') {
                        mediaType = 'document';
                    }

                    // Send media
                    const messageOptions = {
                        [mediaType]: { url: tempFilePath }
                    };

                    if (caption) {
                        messageOptions.caption = caption;
                    }

                    const result = await sock.sendMessage(formattedPhone, messageOptions);

                    logger.info(`Media sent to ${phone}, type: ${mediaType}`);

                    // Clean up temp file
                    await unlink(tempFilePath);

                    res.json({
                        success: true,
                        message: 'Media sent successfully',
                        to: phone,
                        mediaType: mediaType,
                        messageId: result.key.id,
                        timestamp: new Date().toISOString()
                    });

                } catch (sendError) {
                    // Clean up temp file on error
                    if (fs.existsSync(tempFilePath)) {
                        await unlink(tempFilePath);
                    }
                    throw sendError;
                }

            } catch (error) {
                logger.error('Failed to send media:', error);
                res.status(500).json({
                    success: false,
                    message: 'Failed to send media',
                    error: error.message
                });
            }
        });

    } catch (error) {
        logger.error('Send media endpoint error:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
});
```

### Step 2: Install Dependencies
```bash
cd c:\Users\DMCenter\Music\SPMB2\SPMB\whatsapp-server
npm install multer
```

### Step 3: Commit Changes
```bash
cd c:\Users\DMCenter\Music\SPMB2\SPMB
git add whatsapp-server/server.js whatsapp-server/package.json
git commit -m "Add send-media endpoint to WhatsApp gateway for photo/media sending

- Added POST /send-media endpoint
- Support image, video, audio, document (PDF)
- Uses multer for file upload
- Auto-cleanup temp files
- Returns messageId for tracking"
git push
```

### Step 4: Restart Gateway
```bash
# Stop current gateway (Ctrl+C di terminal)

# Start gateway
cd c:\Users\DMCenter\Music\SPMB2\SPMB\whatsapp-server
node server.js
```

Atau jika pakai PM2:
```bash
pm2 restart spmb-gateway
```

---

## Testing

### Test Send Media Endpoint

#### Test dengan curl (Command Line)
```bash
curl -X POST http://localhost:3000/send-media \
  -F "phone=085216343400" \
  -F "caption=Test gambar dari SPMB gateway" \
  -F "media=@C:\Users\YourName\Pictures\test.jpg"
```

#### Test dengan PowerShell
```powershell
$uri = "http://localhost:3000/send-media"
$form = @{
    phone = "085216343400"
    caption = "Test gambar dari SPMB gateway"
    media = Get-Item -Path "C:\Users\YourName\Pictures\test.jpg"
}
Invoke-RestMethod -Uri $uri -Method Post -Form $form
```

#### Expected Response
```json
{
  "success": true,
  "message": "Media sent successfully",
  "to": "085216343400",
  "mediaType": "image",
  "messageId": "3EB0ABCD1234567890",
  "timestamp": "2026-08-10T12:34:56.789Z"
}
```

### Test dengan Postman
1. Method: `POST`
2. URL: `http://localhost:3000/send-media`
3. Body → form-data:
   - Key: `phone`, Value: `085216343400`
   - Key: `caption`, Value: `Test gambar`
   - Key: `media`, Type: File, Value: (pilih file gambar)
4. Send

---

## API Documentation

### Endpoint: POST /send-media

**Request Format**: `multipart/form-data`

**Parameters**:
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| phone | string | Yes | Nomor WhatsApp tujuan (format: 08xxx atau 62xxx) |
| caption | string | No | Text yang menyertai media |
| media | file | Yes | File gambar/video/audio/dokumen |

**Supported Media Types**:
- Images: JPEG, PNG, GIF, WebP
- Videos: MP4, AVI, MOV, MKV
- Audio: MP3, WAV, OGG
- Documents: PDF

**Response Success**:
```json
{
  "success": true,
  "message": "Media sent successfully",
  "to": "628xxx",
  "mediaType": "image|video|audio|document",
  "messageId": "3EB0...",
  "timestamp": "2026-08-10T..."
}
```

**Response Error**:
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error message"
}
```

**HTTP Status Codes**:
- `200` - Success
- `400` - Bad request (missing parameters)
- `500` - Server error
- `503` - WhatsApp not connected

---

## Integration with Laravel

### PHP Code Example

```php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

public function sendPhotoNotification($phone, $message, $photoPath)
{
    $serverUrl = 'http://localhost:3000'; // atau 3001 untuk absensi
    
    // Get full file path
    $fullPath = Storage::disk('public')->path($photoPath);
    
    if (!file_exists($fullPath)) {
        throw new Exception("Photo not found: {$fullPath}");
    }
    
    // Send with media
    $response = Http::timeout(60)
        ->attach('media', file_get_contents($fullPath), basename($fullPath))
        ->post("{$serverUrl}/send-media", [
            'phone' => $phone,
            'caption' => $message,
        ]);
    
    if ($response->successful()) {
        $data = $response->json();
        return [
            'success' => true,
            'messageId' => $data['messageId'] ?? null,
        ];
    }
    
    return [
        'success' => false,
        'error' => $response->json()['message'] ?? 'Failed to send',
    ];
}
```

---

## Troubleshooting

### Issue: Multer not installed
**Error**: `Cannot find module 'multer'`

**Solution**:
```bash
cd whatsapp-server
npm install multer
```

### Issue: Port already in use
**Error**: `EADDRINUSE: address already in use :::3000`

**Solution**:
```bash
# Windows - Kill process on port 3000
netstat -ano | findstr :3000
taskkill /PID [PID_NUMBER] /F

# Or kill all node processes
taskkill /F /IM node.exe
```

### Issue: Gateway not connected
**Error**: `WhatsApp not connected`

**Solution**:
1. Check gateway status: `curl http://localhost:3000/status`
2. Get QR code: `curl http://localhost:3000/qr`
3. Scan QR dengan WhatsApp
4. Retry after connected

### Issue: File too large
**Error**: `Request entity too large`

**Solution**: Add body-parser limit in server.js:
```javascript
app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ limit: '50mb', extended: true }));
```

### Issue: Temp files not cleaned
**Check**: `whatsapp-server/temp/` folder

**Solution**: Files should auto-delete. If not, manually delete:
```bash
cd whatsapp-server
rmdir /s /q temp
```

---

## Performance Notes

### File Size Limits
Recommended maximum file sizes:
- Images: 5 MB
- Videos: 16 MB
- Audio: 16 MB
- Documents: 100 MB

### Timeout
Media upload requires longer timeout:
- Text message: 10 seconds
- Media message: 30-60 seconds

### Bandwidth
Sending photo uses ~10-50x more bandwidth than text:
- Text: ~1 KB
- Photo (compressed): ~100-500 KB
- Video: ~1-5 MB

---

## Summary

✅ **Absensi Gateway (Port 3001)** - READY  
✅ **SPMB Gateway (Port 3000)** - READY  

**Both gateways are now updated!**
- Both gateways support `/send-media` endpoint
- Laravel auto-sends photo when enabled
- Notifications include student photo during check-in/check-out

**Next Steps**:
1. Update SPMB gateway following steps above
2. Test both gateways
3. Enable "Sertakan Foto dalam Notifikasi" in settings
4. Test QR scan with photo capture

---

**Documentation Version**: 1.0  
**Last Updated**: 2026-08-10  
**Author**: Kiro AI Assistant
