# 📚 Seeder Kelas X Busana

## 📋 Data yang Akan Dibuat

### 👩‍🏫 Wali Kelas
- **Nama**: Marista Bela Octaviana, S.Pd
- **Email**: marista.bela@smkn1.sch.id
- **Password**: password123
- **Role**: wali_kelas

### 🏫 Kelas
- **Nama Kelas**: X Busana
- **Tingkat**: X
- **Jurusan**: Busana
- **Status**: Active

### 👥 Siswa (13 orang)

| No | NIS  | Nama                            | No HP Ortu     |
|----|------|---------------------------------|----------------|
| 1  | 2011 | ADELIA MAYLATHULHUSNA UJIYANI   | 085216343400   |
| 2  | 2012 | ASIH MAHARANI                   | 085216343400   |
| 3  | 2013 | AURELIA MARETA A                | 085216343400   |
| 4  | 2014 | ELFA DAMIYANTI                  | 085216343400   |
| 5  | 2015 | FATIMAH AZZAHRA                 | 085216343400   |
| 6  | 2016 | JASYINTA PUTRI NIKA             | 085216343400   |
| 7  | 2017 | MUHAMMAD RAMADHAN               | 085216343400   |
| 8  | 2018 | NADIA HASNA RAHMATUL LAILI      | 085216343400   |
| 9  | 2019 | NADIA NUR'AINI AULIA            | 085216343400   |
| 10 | 2020 | OKTAVIA ANGGRAINI               | 085216343400   |
| 11 | 2021 | RIZKA ARIFATUN NISA             | 085216343400   |
| 12 | 2022 | SHAFA NIA RAMADHANI             | 085216343400   |
| 13 | 2023 | SIVA LIANA SARI                 | 085216343400   |

## 🚀 Cara Menjalankan

### Opsi 1: Jalankan Seeder Saja (Recommended)
```bash
php artisan db:seed --class=XBusanaSeeder
```

### Opsi 2: Jalankan Semua Seeder
```bash
php artisan db:seed
```

## ✅ Yang Akan Dilakukan Seeder

1. ✅ Membuat user wali kelas (jika belum ada)
2. ✅ Membuat kelas X Busana (jika belum ada)
3. ✅ Menghubungkan wali kelas dengan kelas
4. ✅ Membuat 13 siswa dengan data lengkap
5. ✅ Generate QR Code untuk setiap siswa
6. ✅ Menyimpan QR Code di `storage/app/public/qr_codes/`

## 📝 Fitur Seeder

- **Smart Create**: Jika data sudah ada, akan di-update bukan error
- **Auto QR Code**: Otomatis generate QR Code untuk setiap siswa
- **Tahun Ajaran**: Menggunakan tahun ajaran aktif dari settings
- **Skip Duplicate**: Jika QR Code sudah ada, tidak generate ulang

## 🔍 Verifikasi Data

### Cek Wali Kelas
```bash
php artisan tinker
>>> App\Models\User::where('email', 'marista.bela@smkn1.sch.id')->first()
```

### Cek Kelas
```bash
php artisan tinker
>>> App\Models\AttendanceClass::where('nama_kelas', 'X Busana')->with('students')->first()
```

### Cek Siswa
```bash
php artisan tinker
>>> App\Models\AttendanceStudent::where('kelas_id', function($q) {
    $q->select('id')->from('attendance_classes')->where('nama_kelas', 'X Busana');
})->get()
```

## 🎯 Testing QR Code

Setelah seeder berhasil:

1. Buka `/attendance/students` untuk melihat daftar siswa
2. Download QR Code masing-masing siswa
3. Buka `/` (landing page scanner)
4. Scan QR Code untuk test absensi

## 🗑️ Reset Data (Hati-hati!)

Jika ingin menghapus data X Busana dan mulai dari awal:

```bash
php artisan tinker
```

```php
// Hapus siswa kelas X Busana
$kelas = App\Models\AttendanceClass::where('nama_kelas', 'X Busana')->first();
if ($kelas) {
    App\Models\AttendanceStudent::where('kelas_id', $kelas->id)->delete();
    $kelas->delete();
}

// Hapus wali kelas
App\Models\User::where('email', 'marista.bela@smkn1.sch.id')->delete();

// Jalankan seeder lagi
exit
```

```bash
php artisan db:seed --class=XBusanaSeeder
```

## 📂 File yang Dihasilkan

- **Seeder**: `database/seeders/XBusanaSeeder.php`
- **QR Codes**: `storage/app/public/qr_codes/2011.png` sampai `2023.png`
- **Public URL**: `public/storage/qr_codes/2011.png` sampai `2023.png`

## ⚙️ Konfigurasi

Jika ingin mengubah data, edit file:
```
database/seeders/XBusanaSeeder.php
```

Yang bisa diubah:
- Nama wali kelas
- Email wali kelas
- Password wali kelas
- Data siswa (NIS, nama)
- Nomor HP orang tua

## 🔐 Login Credentials

**Wali Kelas X Busana:**
- Email: `marista.bela@smkn1.sch.id`
- Password: `password123`

Setelah login, wali kelas bisa:
- Melihat siswa kelas X Busana
- Input absensi manual
- Melihat laporan absensi kelas
- Download QR Code siswa

---

**Created**: 2024-01-09  
**For**: Testing Continuous Scan Feature  
**File**: `database/seeders/XBusanaSeeder.php`
