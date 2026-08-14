# Fitur Hari Libur — Integrasi E-Kaldik ↔ Absensi

## Deskripsi
Fitur ini memungkinkan sistem Absensi mendeteksi hari libur secara otomatis berdasarkan data dari E-Kaldik (SIM Kurikulum). Ketika hari libur terdeteksi:
- **Scan QR** otomatis ditolak dengan pesan "Hari ini libur"
- **Auto-alpha** (penandaan tidak hadir otomatis) dilewati
- **Banner libur** ditampilkan di halaman scanner dan landing page publik

## Cara Kerja

### Sumber Data
Data hari libur berasal dari tabel `activities` di E-Kaldik yang memiliki `activity_type.is_holiday = true`.

### Alur Sync
1. E-Kaldik menyediakan API endpoint: `GET /api/holidays`
2. Absensi menyimpan data libur di tabel lokal `holidays`
3. Sync bisa dilakukan:
   - **Manual**: Klik tombol "Sinkron dari E-Kaldik" di halaman Hari Libur
   - **Otomatis**: Cron job `holidays:sync` jalan setiap hari jam 06:00 WIB
   - **CLI**: `php artisan holidays:sync`

### Fallback
Jika E-Kaldik down, sistem Absensi tetap berjalan normal karena data libur disimpan di database lokal.

## Konfigurasi

### File `.env` di Absensi
```env
EKALDIK_BASE_URL=https://simkur.smkpgriblora.sch.id
EKALDIK_API_KEY=your-secret-key-here
```

### Cron Job (server Absensi)
```bash
* * * * * cd /path/to/absensi && php artisan schedule:run >> /dev/null 2>&1
```

## Halaman UI
- **Path**: `/holidays` (admin only)
- **Fitur**:
  - Tabel daftar hari libur dengan status (Berlangsung/Mendatang/Selesai)
  - Tombol "Sinkron dari E-Kaldik" (AJAX loading)
  - Tombol "Tambah Manual" (modal form)
  - Hapus hari libur
  - Badge sumber: E-Kaldik (biru) / Manual (hijau)
  - Info terakhir sync

## File Terkait

### E-Kaldik
| File | Keterangan |
|---|---|
| `app/Http/Controllers/Api/HolidayApiController.php` | API endpoint holidays |
| `routes/web.php` | Route registrasi `/api/holidays` |

### Absensi
| File | Keterangan |
|---|---|
| `database/migrations/xxxx_create_holidays_table.php` | Migration tabel holidays |
| `app/Models/Holiday.php` | Model dengan helper `isHoliday()`, `getForDate()` |
| `app/Services/EkaldikHolidayService.php` | Service sync + cek holiday |
| `app/Http/Controllers/HolidayController.php` | Controller CRUD + sync |
| `app/Console/Commands/SyncHolidays.php` | Artisan command sync |
| `resources/views/holidays/index.blade.php` | Halaman UI daftar libur |
| `resources/views/layouts/sidebar.blade.php` | Menu sidebar |
| `resources/views/attendance/scanner.blade.php` | Banner libur di scanner |
| `resources/views/welcome.blade.php` | Banner libur di landing page |
| `app/Services/AttendanceService.php` | Block scan + skip alpha |
| `routes/web.php` | Route holidays |
| `routes/console.php` | Scheduler auto-sync |
| `config/services.php` | Config `ekaldik.base_url` |

## Struktur Tabel `holidays`

| Column | Type | Keterangan |
|---|---|---|
| id | bigint PK | Auto increment |
| name | varchar(255) | Nama kegiatan libur |
| start_date | date | Tanggal mulai |
| end_date | date | Tanggal selesai |
| type | varchar(100) | Jenis (nullable) |
| source | enum | 'ekaldik' atau 'manual' |
| ekaldik_activity_id | bigint | ID dari E-Kaldik (nullable) |
| description | text | Keterangan (nullable) |
| is_active | boolean | Default true |
| synced_at | timestamp | Waktu terakhir sync (nullable) |
