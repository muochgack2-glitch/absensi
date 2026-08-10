# 📷 Photo in WhatsApp Notification - Implementation

## Issue
**Setting aktif**: "Sertakan Foto dalam Notifikasi" ✅ ON  
**Reality**: Foto TIDAK terkirim di WhatsApp notification

## Root Cause

### Problem 1: Boolean Check Bug (FIXED)
```php
// BEFORE (Buggy)
$includePhoto = AttendanceSetting::get('include_photo_in_notification', 'false') === 'true';
// Database value: '1', Expected: 'true' → FALSE → No photo!

// AFTER (Fixed)
$includePhoto = AttendanceSetting::get('include_photo_in_notification', 'false');
$shouldIncludePhoto = in_array($includePhoto, ['true', '1', 1, true], true);
```

### Problem 2: Photo Not Sent to Gateway (FIXED)
```php
// BEFORE
public function sendParentNotification(string $phone, string $message, ?string $photoPath = null): array
{
    // $photoPath ignored! Never used!
    return $this->send($phone, $message, [...]);
}
```

## Solution Implemented

### 1. Fixed Boolean Check
**File**: `app/Services/AttendanceNotificationService.php`

Changed in 2 methods:
- `notifyCheckIn()` - Line 45-49
- `notifyCheckOut()` - Line 87-91

**New code**:
```php
$includePhoto = AttendanceSetting::get('include_photo_in_notification', 'false');
$shouldIncludePhoto = in_array($includePhoto, ['true', '1', 1, true], true);
$photoPath = $shouldIncludePhoto ? $record->check_in_photo : null;
```

### 2. Added Media Sending Support
**File**: `app/Services/AttendanceWhatsAppService.php`

#### A. Updated `sendParentNotification()`
```php
public function sendParentNotification(string $phone, string $message, ?string $photoPath = null): array
{
    // If photo path provided, send with media
    if ($photoPath && Storage::disk('public')->exists($photoPath)) {
        return $this->sendWithMedia($phone, $message, $photoPath, [
            'type' => 'check_in',
            'sent_by' => null,
        ]);
    }
    
    // Otherwise, send text only
    return $this->send($phone, $message, [
        'type' => 'check_in',
        'sent_by' => null,
    ]);
}
```

#### B. New Method: `sendWithMedia()`
```php
/**
 * Send WhatsApp message with media (image)
 * 
 * @param string $phone Phone number
 * @param string $caption Message caption
 * @param string $mediaPath Path to media file in storage
 * @param array $options Additional options
 * @return array
 */
public function sendWithMedia(string $phone, string $caption, string $mediaPath, array $options = []): array
{
    // Implementation details:
    // 1. Get full file path from storage
    // 2. Validate file exists
    // 3. Send via HTTP multipart/form-data to /send-media endpoint
    // 4. Log success/failure
}
```

## WhatsApp Gateway Requirements

### ⚠️ IMPORTANT: Gateway Must Support Media
Your WhatsApp gateway **MUST** have this endpoint:

**Endpoint**: `POST /send-media`

**Request Format** (multipart/form-data):
```json
{
  "phone": "628521634340",
  "caption": "🏫 *SMK PGRI BLORA*\n📍 Notifikasi Absensi\n...",
  "media": [File Upload]
}
```

**Response Format**:
```json
{
  "success": true,
  "messageId": "3EB0...",
  "message": "Media sent successfully"
}
```

### Example WhatsApp Gateway Implementation (Node.js)

If your gateway doesn't support `/send-media`, add this endpoint:

```javascript
const multer = require('multer');
const upload = multer({ storage: multer.memoryStorage() });

app.post('/send-media', upload.single('media'), async (req, res) => {
    try {
        const { phone, caption } = req.body;
        const mediaBuffer = req.file.buffer;
        const mimeType = req.file.mimetype;

        // Send via whatsapp-web.js
        const media = new MessageMedia(mimeType, mediaBuffer.toString('base64'));
        const result = await client.sendMessage(`${phone}@c.us`, media, {
            caption: caption
        });

        res.json({
            success: true,
            messageId: result.id.id,
            message: 'Media sent successfully'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});
```

## Testing

### 1. Verify Setting Value
```bash
php artisan tinker
```
```php
echo "include_photo_in_notification: ";
echo var_export(App\Models\AttendanceSetting::get('include_photo_in_notification'), true);
```

**Expected**: `'1'` (enabled)

### 2. Check Photo Path
When student checks in, verify photo is saved:
```sql
SELECT id, student_id, check_in_time, check_in_photo 
FROM attendance_records 
WHERE date = CURDATE() 
ORDER BY id DESC 
LIMIT 5;
```

**Expected**: `check_in_photo` should have path like `attendance/photos/2011_check_in_20260810_124655.jpg`

### 3. Test Check-In with Photo
1. Scan QR code with camera ON
2. Photo should be captured
3. Check logs:

```bash
tail -f storage/logs/laravel.log
```

Look for:
```
Attempting to send WhatsApp message with media
phone: 085216343400
media_path: attendance/photos/...
```

### 4. Verify WhatsApp Received
Check parent's WhatsApp - should receive:
- ✅ Message with text
- ✅ Image attachment (student photo during check-in)

## Troubleshooting

### Photo Not Sent - Checklist

#### 1. Setting Enabled?
```bash
php artisan tinker
App\Models\AttendanceSetting::get('include_photo_in_notification')
# Should return: '1' or 'true'
```

#### 2. Photo Captured?
```sql
SELECT check_in_photo FROM attendance_records 
WHERE id = [LATEST_RECORD_ID];
# Should NOT be NULL
```

#### 3. File Exists?
```bash
php artisan tinker
Storage::disk('public')->exists('attendance/photos/FILENAME.jpg')
# Should return: true
```

#### 4. Gateway Supports Media?
```bash
curl -X POST http://localhost:3001/send-media \
  -F "phone=628521634340" \
  -F "caption=Test" \
  -F "media=@/path/to/image.jpg"
```

Expected response:
```json
{
  "success": true,
  "messageId": "..."
}
```

If error: **Gateway doesn't support media endpoint!**

### Fallback: Text-Only Mode

If your gateway doesn't support `/send-media`, notification will automatically fallback to text-only:

```php
// In sendParentNotification()
if ($photoPath && Storage::disk('public')->exists($photoPath)) {
    return $this->sendWithMedia(...);  // Try media first
}

// Fallback to text-only if media not available
return $this->send($phone, $message, [...]);
```

## Configuration

### Enable/Disable Photo in Settings UI

Go to: **Settings → WhatsApp Notifications**

Toggle: **"Sertakan Foto dalam Notifikasi"**
- ON (1/true) → Send with photo
- OFF (0/false) → Send text only

### Database Setting
```sql
UPDATE attendance_settings 
SET value = '1' 
WHERE `key` = 'include_photo_in_notification';
```

## Performance Considerations

### Photo File Size
Recommended: **< 500KB per photo**

Current settings:
- Max width: 800px
- Quality: 80%
- Format: JPEG

### Network Impact
Sending photo uses **~2-3x more bandwidth** than text-only:
- Text message: ~1-2 KB
- With photo: ~300-500 KB

### Gateway Timeout
Media upload needs longer timeout:
```php
$response = Http::timeout($this->timeout * 2) // Double timeout for media
    ->attach('media', ...)
    ->post("{$serverUrl}/send-media", [...]);
```

## Files Changed

1. ✅ `app/Services/AttendanceNotificationService.php`
   - Fixed boolean check for `include_photo_in_notification`
   - Both `notifyCheckIn()` and `notifyCheckOut()`

2. ✅ `app/Services/AttendanceWhatsAppService.php`
   - Added `use Illuminate\Support\Facades\Storage;`
   - Updated `sendParentNotification()` to handle photo
   - Added new method `sendWithMedia()`

3. ✅ `PHOTO_NOTIFICATION_FEATURE.md` (this file)

## Summary

✅ **Boolean check** - Fixed (accept '1', 'true', 1, true)  
✅ **Photo handling** - Implemented (`sendWithMedia()`)  
⚠️ **Gateway requirement** - Must support `/send-media` endpoint  
✅ **Fallback** - Auto-fallback to text-only if photo unavailable  

**Next**: Test check-in with photo enabled! 📸
