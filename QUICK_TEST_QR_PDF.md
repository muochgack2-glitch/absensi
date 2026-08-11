# Quick Test: Kartu QR PDF (5 menit)

## Prerequisites
- Application running: `php artisan serve` (port 8000)
- Database seeded dengan siswa

## Quick Test Steps

### 1. Generate QR Codes (if not exists)
```bash
php artisan attendance:generate-qr
```
**Output**: "Berhasil generate X QR Code"

---

### 2. Test Single Student QR Card

#### 2.1 Show Page
```
Browser: http://localhost:8000/attendance/qr/12345
(ganti 12345 dengan NIS dari database)
```
✅ **Expected**: Halaman menampilkan kartu QR besar dengan info siswa

#### 2.2 Preview Card HTML
```
Browser: http://localhost:8000/attendance/qr/12345/preview-card
```
✅ **Expected**: Tampilkan kartu 50mm x 50mm dengan QR dan teks

#### 2.3 Download PDF
```
Dari /attendance/qr/{nis}:
Klik tombol "Download PDF Card"
```
✅ **Expected**: File `QR_Kartu_{NIS}_{Nama}.pdf` terdownload
- Ukuran A4 portrait
- Kartu 50mm x 50mm di tengah

---

### 3. Test Bulk QR Cards PDF

#### 3.1 Get Class ID dari Database
```bash
php artisan tinker
>>> App\Models\AttendanceClass::pluck('id', 'nama_kelas');
# Output: [1 => "X - AKL", 2 => "X - RPL", ...]
```
Catat class_id untuk test

#### 3.2 Preview Bulk (HTML)
```
Browser: http://localhost:8000/attendance/qr/cards-preview?class_id=1&layout=3x3&include_class=true
(ganti class_id sesuai)
```
✅ **Expected**: 
- Grid 3x3 (9 kartu per halaman)
- Multiple halaman untuk banyak siswa
- Setiap kartu: 50mm x 50mm

#### 3.3 Download Bulk (PDF) - via curl
```bash
curl -X POST "http://localhost:8000/attendance/qr/cards-pdf" \
  -d "class_id=1&layout=3x3&include_class=1" \
  -o test_qr.pdf

# Check file size
ls -lh test_qr.pdf
```
✅ **Expected**: File generated, size > 100KB

#### 3.4 Download via Browser Form
```
1. Go to: http://localhost:8000/attendance/students
2. Cari tombol "Cetak Kartu QR (PDF)"
3. Select class & layout
4. Klik "Download PDF"
```
✅ **Expected**: PDF download sebagai `QR_Kartu_Siswa_*.pdf`

---

### 4. Visual Comparison (Optional)

#### Compare Preview vs Download:
```bash
# 1. Screenshot preview HTML (Chrome)
# 2. Open downloaded PDF
# 3. Compare first 3 cards on each

# Check visual match:
# - Ukuran kartu
# - QR position
# - Font size
# - Spacing
```

✅ **Expected**: Layout 95%+ match

---

### 5. Scan Test (Optional)

#### Test QR Scanability:
```
1. Generate PDF untuk 1 siswa
2. Print 1 halaman
3. Scan QR dengan QR scanner atau camera
4. Verify NIS extracted correctly
```

✅ **Expected**: NIS terbaca dengan benar

---

## Shortcuts

### View QR Page for Any Student
```bash
php artisan tinker
>>> $student = App\Models\AttendanceStudent::first();
>>> route('attendance.qr.show', $student->nis);
# Copy output URL dan buka di browser
```

### Download PDF for Class 1
```bash
# Direct curl download
curl -X POST "http://localhost:8000/attendance/qr/cards-pdf" \
  -d "class_id=1" \
  -H "X-Requested-With: XMLHttpRequest" \
  -o qr_cards.pdf && echo "✅ Done" || echo "❌ Failed"
```

### Check PDF Generated Properly
```bash
# Linux/Mac: Check PDF is valid
file qr_cards.pdf

# Count pages
pdfinfo qr_cards.pdf | grep Pages
```

### Debug: Check QR Files
```bash
# List all generated QR files
ls -la storage/app/public/attendance/qrcodes/

# Count QR files
ls storage/app/public/attendance/qrcodes/ | wc -l
```

---

## Common Issues & Fix

| Issue | Fix |
|-------|-----|
| PDF tidak ada | Run `php artisan attendance:generate-qr` |
| QR tidak muncul di PDF | Install Imagick: `apt-get install php-imagick` |
| "Memory exhausted" | Increase memory_limit di `.env`: `PHP_MEMORY_LIMIT=512M` |
| Layout mismatch | Run `php artisan view:clear` |
| Slow download (>30s) | Check server resources / reduce student count |

---

## Success Indicators

✅ Preview halaman load dalam < 2 detik
✅ Single PDF download dalam < 5 detik  
✅ Bulk PDF (100 siswa) dalam < 20 detik
✅ QR codes scannable
✅ Teks readable di A4 print
✅ No error messages

---

**Time: ~5 minutes for basic validation**

For detailed test plan, see: `TESTING_KARTU_QR_PDF.md`
