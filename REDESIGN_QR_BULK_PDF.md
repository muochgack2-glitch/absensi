# Redesign: QR Code Bulk Generate dengan PDF Kartu Siswa

## Overview
Ubah fitur bulk generate QR code agar output-nya berupa **PDF dengan kartu siswa** dalam grid layout (4x4 atau 6x6 per A4 sheet), bukan hanya menyimpan SVG individual.

## Requirements

### Fitur Utama

#### 1. **Select Grid Layout**
- User bisa pilih layout: 4x4 (16 kartu/halaman) atau 6x6 (36 kartu/halaman)
- Display preview masing-masing layout

#### 2. **Kartu Siswa Design**
Setiap kartu berisi:
- **QR Code** (SVG/PNG, ukuran proporsional dengan grid)
- **NIS** (di bawah QR, monospace font untuk readability)
- **Nama Siswa** (small font, centered)
- **Kelas** (optional, very small font)
- **Border** (light gray, separating each card)

**Card Dimensions:**
- **Ukuran Standar:** 5cm x 6cm per card (50mm x 60mm)
- **Layout:** 3x3 (9 kartu per halaman A4)

#### 3. **PDF Generation**
- Generate PDF dengan semua siswa aktif (atau selected class)
- Automatic page breaks sesuai grid layout
- Page margins: 10mm
- Landscape atau Portrait? → **Portrait** (default untuk A4)

#### 4. **Options Sebelum Generate**
Modal/Form dengan:
- [ ] **Select Class** - Generate hanya siswa kelas tertentu (or all active students)
- [ ] **Select Layout** - Radio button: 4x4 atau 6x6
- [ ] **Include Class Name** - Checkbox untuk tampilin nama kelas di kartu
- [ ] **Preview** - Button untuk preview PDF sebelum download

#### 5. **Download & Naming**
- Filename format: `QR_Kartu_Siswa_[ClassCode]_[Date].pdf`
  - Contoh: `QR_Kartu_Siswa_X-AKALBR_2026-08-11.pdf`
- Direct download via browser

---

## Implementation Plan

### 1. **Backend: PDF Generation Service**
File: `app/Services/QRCardPdfService.php`

**Methods:**
```php
public function generatePDF(
    array $students, 
    string $layout = '4x4',  // or '6x6'
    bool $includeClass = false
): \Barryvdh\DomPDF\Facade\Pdf

public function getLayoutDimensions(string $layout): array
```

**Libraries:**
- `barryvdh/laravel-dompdf` (already in use for Excel export)
- `SVG rendering` (or convert SVG QR to PNG first)

### 2. **Backend: Controller Method**
File: `app/Http/Controllers/AttendanceQRController.php`

**New Method:**
```php
public function generateCardsPDF(Request $request)
{
    $classId = $request->input('class_id'); // optional
    $layout = $request->input('layout', '4x4');
    $includeClass = $request->boolean('include_class', false);
    
    // Get students
    $query = AttendanceStudent::where('is_active', true);
    if ($classId) {
        $query->where('class_id', $classId);
    }
    $students = $query->orderBy('kelas_id')->orderBy('nis')->get();
    
    // Generate PDF
    $pdf = app(QRCardPdfService::class)
        ->generatePDF($students, $layout, $includeClass);
    
    return $pdf->download("QR_Kartu_Siswa_{$classId}_{now()->format('Y-m-d')}.pdf");
}
```

**Route:**
```php
Route::post('/attendance/qr/cards-pdf', [AttendanceQRController::class, 'generateCardsPDF'])
    ->name('attendance.qr.cards-pdf');
```

### 3. **Frontend: Modal/Form**
File: `resources/views/attendance/students/index.blade.php`

**Update Modal:**
```blade
<!-- Modal Generate Cards PDF -->
<div id="modalQRCardsPDF" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
            Generate Kartu QR (PDF)
        </h3>
        
        <form id="formQRCardsPDF" method="POST" action="{{ route('attendance.qr.cards-pdf') }}">
            @csrf
            
            <!-- Select Class -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Kelas (Opsional - biarkan kosong untuk semua siswa aktif)
                </label>
                <select name="class_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">-- Semua Siswa Aktif --</option>
                    @foreach(\App\Models\AttendanceClass::orderBy('nama_kelas')->get() as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Select Layout -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Layout Kartu per Halaman
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="layout" value="4x4" checked class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                            4x4 (16 kartu/halaman) - Ukuran besar, lebih jelas
                        </span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="layout" value="6x6" class="w-4 h-4 text-blue-600">
                        <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                            6x6 (36 kartu/halaman) - Compact, lebih hemat kertas
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Include Class Name -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="include_class" value="1" class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                        Tampilkan nama kelas di kartu
                    </span>
                </label>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl font-semibold text-sm text-white bg-red-600 hover:bg-red-700 transition-all">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </button>
                <button type="button" onclick="document.getElementById('modalQRCardsPDF').classList.add('hidden')" class="flex-1 py-2 px-4 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
```

### 4. **Button to Open Modal**
Add button in students page header:
```blade
<button
    type="button"
    onclick="document.getElementById('modalQRCardsPDF').classList.remove('hidden')"
    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 shadow-md text-white"
    style="background: linear-gradient(to right, #dc2626, #991b1b);"
>
    <i class="fas fa-file-pdf mr-2"></i>
    Cetak Kartu QR (PDF)
</button>
```

---

## Design Details

### Card Layout (3x3)
```
┌─────────────────┐
│   [QR 3x3cm]    │
│   ┌─────────┐   │
│   │         │   │
│   │   QR    │   │
│   │         │   │
│   └─────────┘   │
│    NIS: 12345   │
│  Nama Siswa     │
│  X-AKALBR       │ (optional)
└─────────────────┘
```

**Spesifikasi Teknis:**
- **Ukuran Kartu:** 5cm (lebar) x 6cm (tinggi) = 50mm x 60mm
- **Layout:** 3 kolom x 3 baris = 9 kartu per halaman A4
- **QR Code:** 47mm x 47mm (maksimal, rata ke atas)
- **NIS:** 10pt (monospace, bold)
- **Nama / Kelas:** 9pt (1 baris format: "Agus Setiawan / X-AKALBR")
- **Sekolah:** 9pt (baru diperbesar dari 8pt)
- **Spacing:** 4mm antar kartu
- **Total halaman:** ~13 halaman untuk 100 siswa (100 ÷ 9 = 11.1 halaman)

---

## Implementation Checklist

- [x] Create `QRCardPdfService.php` with PDF generation logic
- [x] Create Blade template for PDF layout (`resources/views/pdfs/qr-cards.blade.php`)
- [x] Add new route `attendance.qr.cards-pdf`
- [x] Add new controller method `generateCardsPDF()`
- [x] Update modal in `students/index.blade.php`
- [x] Add button in students page header
- [ ] Test PDF generation with different layouts
- [ ] Test with different screen sizes and browsers
- [ ] Handle error cases (no students, invalid class)
- [ ] Document the feature in user guide

---

## Notes

- Pertimbangkan ukuran QR code yang optimal untuk scanning (min 2cm)
- Font size harus readable di kartu kecil (min 8pt)
- Border dan spacing harus konsisten
- Warna background? White atau dengan light background?
- Apakah perlu tambah logo sekolah di setiap kartu atau header halaman?
