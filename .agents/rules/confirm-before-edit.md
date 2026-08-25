# Confirm Before Any Code Change

Sebelum melakukan **perubahan apapun pada kode** (edit file, create file, delete file),
selalu tampilkan rencana perubahan terlebih dahulu dan tunggu konfirmasi eksplisit dari user.

Ini berlaku untuk:
- Perubahan 1 baris (trivial) sekalipun
- Bug fix sederhana
- Perubahan konfigurasi/nilai konstanta
- Penambahan file baru

**Pengecualian:**
- Membuat artifact/dokumen (bukan kode sumber)
- Menjalankan command read-only (grep, ls, cat, dll.)
- Perintah git pull, git status, git log (tidak mengubah kode)

**Format konfirmasi:** Tampilkan diff/preview perubahan dan jelaskan alasannya.
Tunggu user bilang "oke", "lanjut", "eksekusi", atau sejenisnya sebelum menjalankan edit.
