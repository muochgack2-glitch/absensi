# 📝 Input Manual Absensi - Quick Reference

## Apa Itu?
Fitur untuk **admin/guru menandai kehadiran siswa secara manual** tanpa QR code scan.

## Kapan Digunakan?
```
✅ Siswa lupa HP / HP rusak
✅ Sistem QR code error / internet mati
✅ Input izin/sakit (ada surat dari ortu)
✅ Koreksi data salah
✅ Absensi event khusus (upacara, ujian)
✅ Siswa datang sangat terlambat (setelah cutoff)
```

## Status yang Bisa Diinput
```
H = ✅ Hadir       (hijau)
T = ⏰ Terlambat   (kuning)
I = 🏥 Izin        (biru)
S = 🏥 Sakit       (ungu)
A = ❌ Alpha       (merah)
— = ⏭️ Skip        (abu-abu, tidak diubah)
```

---

## Cara Pakai (3 Langkah)

### 1. Pilih Filter
```
[Tanggal: 11-08-2026] [Kelas: X RPL 1] [Tampilkan]
```

### 2. Input Status
```
┌────┬──────────┬──────┬──────────┬─────────┬──────────────┐
│ No │ Nama     │ NIS  │  Status  │  Jam    │ Keterangan   │
├────┼──────────┼──────┼──────────┼─────────┼──────────────┤
│ 1  │ Ahmad    │ 001  │ H T I    │ 07:10   │ -            │
│    │          │      │ S A —    │         │              │
│    │          │      │ (klik H) │         │              │
├────┼──────────┼──────┼──────────┼─────────┼──────────────┤
│ 2  │ Budi     │ 002  │ H T I    │ -       │ Sakit demam  │
│    │          │      │ S A —    │         │              │
│    │          │      │ (klik S) │         │              │
└────┴──────────┴──────┴──────────┴─────────┴──────────────┘
```

### 3. Simpan
```
[Simpan Absensi] ← Klik tombol ini
```

---

## Fitur Quick Actions

### Isi Semua (Bulk)
```
[Hadir Semua] [Izin Semua] [Sakit Semua] [Alpha Semua]

Contoh:
1. Event sekolah → Klik "Hadir Semua"
2. Manual ubah yang tidak hadir jadi Alpha
3. Simpan
```

### Update Record
```
Jika siswa sudah scan QR:
- Row highlight biru
- Bisa ubah statusnya
- Sistem UPDATE (bukan insert duplikat)
```

### Delete Record
```
[🗑️] ← Klik untuk hapus record yang salah
```

---


## Skenario Lengkap

### Skenario 1: Siswa Lupa HP
```
Masalah: Ahmad datang jam 07:10 tapi lupa bawa HP

Solusi:
1. Buka Input Manual Absensi
2. Pilih: Tanggal = Hari ini, Kelas = X RPL 1
3. Cari nama "Ahmad"
4. Klik status H (Hadir)
5. Isi jam: 07:10
6. Simpan

Hasil: ✅ Ahmad tercatat hadir
```

### Skenario 2: Koreksi Status
```
Masalah: Budi scan QR → TERLAMBAT, tapi ada surat dokter

Solusi:
1. Buka Input Manual Absensi
2. Pilih tanggal + kelas
3. Row Budi sudah highlight biru (ada record)
4. Klik status I (Izin)
5. Keterangan: "Surat dokter"
6. Simpan

Hasil: ✅ Status berubah TERLAMBAT → IZIN
```

### Skenario 3: Event Sekolah
```
Masalah: Upacara bendera, semua siswa hadir tanpa QR scan

Solusi:
1. Buka Input Manual Absensi
2. Pilih tanggal + kelas
3. Klik [Hadir Semua] (tombol hijau)
4. Manual ubah siswa yang tidak hadir → Alpha
5. Simpan

Hasil: ✅ Semua siswa tercatat dalam 1x klik
```

---

## Perbedaan: Manual vs QR Scan

| Aspek | QR Scan | Input Manual |
|-------|---------|--------------|
| **Siapa** | Siswa | Admin/Guru |
| **Device** | HP siswa | PC admin |
| **Foto** | ✅ Ada | ❌ Tidak ada |
| **Notif WA** | ✅ Auto | ❌ Tidak auto |
| **Jam** | Real-time | Manual input |
| **Bulk** | ❌ Tidak bisa | ✅ Bisa |
| **Use Case** | Harian | Emergency |

---

## Important Notes

### ✅ DO
- Gunakan untuk situasi darurat
- Isi keterangan jika perlu
- Double check sebelum simpan
- Gunakan bulk operation

### ❌ DON'T
- Jangan gunakan sebagai metode utama
- Jangan input tanpa verifikasi
- Jangan lupa isi jam masuk
- Jangan delete record sembarangan

---

## FAQ Singkat

**Q: Apakah input manual kirim WA ke ortu?**  
A: ❌ TIDAK otomatis. Manual input untuk koreksi/backdated.

**Q: Bisa input tanggal kemarin?**  
A: ✅ BISA. Pilih tanggal yang diinginkan di filter.

**Q: Bisa update record yang sudah ada?**  
A: ✅ BISA. Sistem auto-detect, jika ada → UPDATE, jika tidak → INSERT.

**Q: Apa bedanya status — (Skip)?**  
A: Status Skip = Tidak diubah, lewati baris ini.

**Q: Bisa isi 1 kelas sekaligus?**  
A: ✅ BISA. Gunakan tombol "Isi Semua" lalu adjust yang perlu.

**Q: Siapa yang bisa akses?**  
A: Admin, Guru, Wali Kelas. Siswa & ortu tidak bisa.

---

## Flow Chart

```
┌─────────────────────┐
│   Siswa Tidak Bisa  │
│      Scan QR?       │
└──────────┬──────────┘
           │
           ▼
   ┌───────────────┐
   │ Buka Manual   │
   │    Input      │
   └───────┬───────┘
           │
           ▼
   ┌───────────────┐
   │ Pilih Tanggal │
   │   & Kelas     │
   └───────┬───────┘
           │
           ▼
   ┌───────────────┐
   │ Pilih Status  │
   │ (H/T/I/S/A)   │
   └───────┬───────┘
           │
           ▼
   ┌───────────────┐
   │  Isi Jam &    │
   │  Keterangan   │
   └───────┬───────┘
           │
           ▼
   ┌───────────────┐
   │    SIMPAN     │
   └───────┬───────┘
           │
           ▼
   ┌───────────────┐
   │ ✅ Tersimpan  │
   │   Database    │
   └───────────────┘
```

---

## Technical Quick Ref

### Routes
```
GET  /attendance/manual              → Form input
POST /attendance/manual              → Simpan
DELETE /attendance/manual/{record}   → Hapus
```

### Controller Method
```php
index()   → Tampilkan form + data existing
store()   → Simpan/update absensi
destroy() → Hapus record
```

### Logic
```php
foreach ($entries as $entry) {
    if ($entry['status'] === 'skip') {
        continue; // Lewati
    }
    
    AttendanceRecord::updateOrCreate(
        ['student_id' => $id, 'date' => $date],
        ['status' => $status, 'check_in_time' => $time]
    );
}
```

---

## 🎯 Remember

**Input Manual = Backup Method**

- QR Scan tetap metode utama ✅
- Manual input untuk emergency saja ⚠️
- Tidak ada foto & WA notification ❌
- Tapi bisa bulk operation & koreksi data ✅

---

**📚 Dokumentasi Lengkap:** `PANDUAN_INPUT_MANUAL_ABSENSI.md`  
**🔧 Settings:** `PANDUAN_FITUR_SETTINGS.md`  
**📊 Dashboard:** `PANDUAN_DASHBOARD.md`
