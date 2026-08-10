# 🎴 PDF CARD REDESIGN - DISCUSSION

## Current System Overview

### Paper & Layout Options
- **Paper Size**: F4 (215mm × 330mm)
- **Layout Options**:
  - **2×5** (10 cards/page) - ⭐ Direkomendasikan
  - **2×4** (8 cards/page) - Lebih besar
  - **2×3** (6 cards/page) - QR Paling Besar

### Current Card Design Elements

#### 1. Card Header (Blue Gradient)
- School logo (circular)
- School name
- "KARTU PELAJAR [Tahun Ajaran]"

#### 2. Card Body (3 columns)
- **Left Column**: Student photo (18mm × 24mm)
- **Middle Column**: Student info
  - Nama
  - NIS
  - Kelas
  - T.A. (Tahun Ajaran)
- **Right Column**: QR Code (22mm × 22mm)

#### 3. Card Footer
- School address (gray background)

### Current Card Dimensions
- **2×5 Layout**: ~58mm height per card
- **2×4 Layout**: ~72mm height per card
- **2×3 Layout**: ~98mm height per card

## Questions for Discussion

### 1. Paper Size
Apakah tetap menggunakan **F4 (215mm × 330mm)**?
- [ ] Ya, tetap F4
- [ ] Ganti ke A4 (210mm × 297mm)
- [ ] Ukuran lain: _____________

### 2. Layout Preference
Layout mana yang Anda inginkan sebagai default?
- [ ] 2×5 (10 cards/page) - Compact
- [ ] 2×4 (8 cards/page) - Medium
- [ ] 2×3 (6 cards/page) - Large QR
- [ ] Layout baru: _____________

### 3. Card Design Elements
Elemen mana yang ingin diubah/ditambah/dihapus?

#### Header
- [ ] Warna gradient (sekarang: blue)
- [ ] Logo position/size
- [ ] Tambah info lain: _____________

#### Body Layout
- [ ] Ukuran foto siswa (sekarang: 18mm × 24mm)
- [ ] Ukuran QR code (sekarang: 22mm × 22mm)
- [ ] Posisi elemen (foto, info, QR)
- [ ] Tambah field info: _____________

#### Footer
- [ ] Tambah tanda tangan kepala sekolah
- [ ] Tambah barcode/nomor unik
- [ ] Tambah info lain: _____________

### 4. Design Style
Gaya desain yang diinginkan:
- [ ] Modern minimalist (seperti sekarang)
- [ ] Formal/resmi dengan border tebal
- [ ] Colorful dengan accent colors per kelas
- [ ] Lainnya: _____________

### 5. Printing Concerns
Ada masalah saat cetak?
- [ ] QR code terlalu kecil
- [ ] Foto tidak jelas
- [ ] Text terlalu kecil
- [ ] Margin tidak pas
- [ ] Lainnya: _____________

## Design Suggestions to Discuss

### Option A: Enlarge QR Code
- Perbesar QR menjadi 28mm × 28mm
- Perkecil foto menjadi 16mm × 20mm
- Lebih mudah scan, cocok untuk F4 2×4 layout

### Option B: Horizontal Layout
- Card landscape orientation
- Photo di kiri, info di tengah, QR di kanan
- Lebih leluasa untuk info tambahan

### Option C: Two-Side Card
- Front: Photo + Basic Info + School Logo
- Back: QR Code (LARGE) + Barcode
- Perlu cetak bolak-balik

### Option D: Add Security Features
- Watermark/background pattern
- Holographic-style border
- Unique ID number per card
- Validity period

## Additional Features to Consider

### 1. Batch Printing Features
- [ ] Print per kelas dengan cover page
- [ ] Automatic page numbering
- [ ] Print date/batch ID
- [ ] Export to multiple formats (PDF, PNG per card)

### 2. Card Information
- [ ] Add student blood type
- [ ] Add emergency contact
- [ ] Add valid until date
- [ ] Add unique card number

### 3. QR Code Enhancement
- [ ] Add QR with URL (not just NIS)
- [ ] Add backup barcode
- [ ] QR with error correction level H
- [ ] Multiple QR sizes for different uses

## Technical Notes

### Current Implementation
- **PDF Generator**: DomPDF (Laravel package)
- **QR Generator**: BaconQrCode with PHP GD (no Imagick required)
- **Layout**: HTML table (DomPDF doesn't support flexbox well)
- **Fonts**: Helvetica, Arial (web-safe)

### Limitations
- ❌ DomPDF tidak support CSS Flexbox/Grid (must use tables)
- ❌ DomPDF tidak support advanced CSS (transforms, filters)
- ⚠️ Large batch prints may take time (memory intensive)
- ✅ SVG QR codes work but PNG is more reliable

### Performance
- Current: ~10-15 cards per second rendering
- Memory: 512MB limit set
- Recommendation: Batch max 100 cards per PDF

---

## Next Steps

**Please provide your preferences above so I can:**
1. Create mockup designs based on your requirements
2. Implement the redesigned card template
3. Update the bulk printing system
4. Test with your actual data

**Screenshot/Sample Request:**
Jika ada contoh kartu yang Anda suka (dari sekolah lain, atau desain online), silakan share untuk referensi!
