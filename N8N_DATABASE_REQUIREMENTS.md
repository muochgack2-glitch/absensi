# 📊 Database Requirements untuk n8n Chatbot

## 🔍 **Database Check Results**

Tanggal: 12 Agustus 2026

---

## ❌ **Masalah yang Ditemukan**

### **1. Field `phone` Tidak Ada di Tabel `users`**

**Struktur tabel `users` saat ini:**
```
users
├── id
├── name
├── email
├── password
├── role (admin/wali_kelas)
├── kelas_id (foreign key ke attendance_classes)
├── remember_token
└── timestamps
```

**Yang kurang:**
- ❌ Field `phone` untuk menyimpan nomor WhatsApp wali kelas

**Dampak:**
- Chatbot tidak bisa identify wali kelas berdasarkan nomor WA yang kirim pesan
- Laravel API `/api/chatbot/summary/{phone}` tidak bisa query data

---

## ✅ **Solusi yang Sudah Dibuat**

### **Migration File: `add_phone_to_users_table.php`**

**Location:** `database/migrations/2026_08_12_000001_add_phone_to_users_table.php`

**Code:**
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone', 20)->nullable()->after('email')->index();
});
```

**Menambahkan:**
- Field `phone` (varchar 20, nullable, indexed)
- Posisi: setelah field `email`

---

### **Update Model User**

**File:** `app/Models/User.php`

**Perubahan:**
```php
// BEFORE
protected $fillable = ['name', 'email', 'password', 'role', 'kelas_id'];

// AFTER
protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'kelas_id'];
```

---

## 🚀 **Cara Apply Changes**

### **Step 1: Run Migration**

```bash
cd /www/wwwroot/absensi

# Production
php artisan migrate --force

# Local development
php artisan migrate
```

**Expected output:**
```
Migrating: 2026_08_12_000001_add_phone_to_users_table
Migrated:  2026_08_12_000001_add_phone_to_users_table (XX.XXms)
```

---

### **Step 2: Verify Migration**

```bash
# Check table structure
php artisan tinker

# Dalam tinker:
Schema::hasColumn('users', 'phone')
// Should return: true

# Check model fillable
$user = new \App\Models\User;
$user->getFillable();
// Should include: "phone"

exit
```

**Atau via SQL:**
```sql
DESCRIBE users;
-- Atau
SHOW COLUMNS FROM users LIKE 'phone';
```

**Expected result:**
```
Field: phone
Type: varchar(20)
Null: YES
Key: MUL (indexed)
```

---

### **Step 3: Update Data Wali Kelas**

**Tambahkan nomor WA untuk wali kelas yang sudah ada:**

#### **Via Tinker:**
```bash
php artisan tinker

# Update nomor WA wali kelas
$waliKelas = \App\Models\User::where('email', 'rina@smkpgriblora.sch.id')->first();
$waliKelas->phone = '6281234567890';  // Format: 62...
$waliKelas->save();

exit
```

#### **Via SQL:**
```sql
UPDATE users 
SET phone = '6281234567890' 
WHERE email = 'rina@smkpgriblora.sch.id';
```

#### **Via Laravel UI (Future):**
Bisa tambahkan form edit profile wali kelas untuk update nomor WA sendiri.

---

## 🎯 **Data Structure Setelah Migration**

### **Tabel `users` (Updated)**
```
users
├── id
├── name
├── email
├── phone                ← NEW!
├── password
├── role
├── kelas_id
└── timestamps
```

### **Relasi untuk Chatbot**

#### **Flow Identify Wali Kelas:**
```
1. Wali Kelas kirim WA dari: 6281234567890
   ↓
2. n8n forward ke Laravel: GET /api/chatbot/summary/6281234567890
   ↓
3. Laravel query:
   - Normalize phone (62xxx atau 08xxx)
   - Find user WHERE phone = '6281234567890' AND role = 'wali_kelas'
   ↓
4. Get kelas via relasi:
   - Cara A: User->kelas (via users.kelas_id)
   - Cara B: AttendanceClass WHERE wali_kelas_id = user.id
   ↓
5. Get students di kelas tersebut
   ↓
6. Get attendance records hari ini
   ↓
7. Format & return response
```

---

## 📋 **Sample Data yang Diperlukan**

### **Format Nomor WA:**

**PENTING:** Gunakan format international **TANPA** tanda `+`:

✅ **BENAR:**
```
6281234567890  (Indonesia)
```

❌ **SALAH:**
```
+6281234567890  (dengan +)
081234567890    (dengan 08)
62-812-3456-7890 (dengan dash)
```

### **Contoh Data Wali Kelas:**

```sql
-- Contoh insert wali kelas baru
INSERT INTO users (name, email, phone, password, role, kelas_id, created_at, updated_at)
VALUES (
  'Bu Rina',
  'rina@smkpgriblora.sch.id',
  '6281234567890',
  '$2y$12$...',  -- hashed password
  'wali_kelas',
  5,  -- ID kelas X AKL
  NOW(),
  NOW()
);
```

---

## 🔄 **Relasi Database Clarification**

### **Ada 2 Cara Mapping Wali Kelas ↔ Kelas:**

#### **Cara 1: `users.kelas_id` → `attendance_classes.id`**
```php
// Di User model
public function kelas() {
    return $this->belongsTo(AttendanceClass::class, 'kelas_id');
}

// Usage
$user->kelas; // Get kelas wali kelas ini
```

#### **Cara 2: `attendance_classes.wali_kelas_id` → `users.id`**
```php
// Di AttendanceClass model
public function waliKelas() {
    return $this->belongsTo(User::class, 'wali_kelas_id');
}

// Usage
$kelas->waliKelas; // Get wali kelas dari kelas ini
```

### **Rekomendasi:**

**Gunakan Cara 1 (`users.kelas_id`)** karena:
- ✅ Sudah ada foreign key constraint
- ✅ Lebih clear: 1 wali kelas = 1 kelas
- ✅ Relasi sudah didefinisikan di User model

**Cara 2 bisa diabaikan atau dijadikan backup** (untuk migrasi data lama jika ada).

---

## 🧪 **Testing Checklist**

Setelah migration, test ini:

### **1. Test Migration Success**
```bash
php artisan migrate:status
# Pastikan ada: 2026_08_12_000001_add_phone_to_users_table [Ran]
```

### **2. Test Data Insert**
```php
$user = \App\Models\User::create([
    'name' => 'Test Wali Kelas',
    'email' => 'test@test.com',
    'phone' => '6289999999999',
    'password' => bcrypt('password'),
    'role' => 'wali_kelas',
    'kelas_id' => 1
]);

echo $user->phone; // Should output: 6289999999999
```

### **3. Test Query by Phone**
```php
$user = \App\Models\User::where('phone', '6289999999999')->first();
echo $user->name; // Should output: Test Wali Kelas
```

### **4. Test Relasi**
```php
$user = \App\Models\User::where('role', 'wali_kelas')->first();
echo $user->kelas->nama_kelas; // Should output nama kelas
echo $user->kelas->students()->count(); // Should output jumlah siswa
```

---

## 📊 **Contoh Query untuk Populate Data**

### **Query 1: Lihat Semua Wali Kelas**
```sql
SELECT 
    u.id,
    u.name,
    u.email,
    u.phone,
    ac.nama_kelas,
    ac.tingkat,
    COUNT(ast.id) as jumlah_siswa
FROM users u
LEFT JOIN attendance_classes ac ON u.kelas_id = ac.id
LEFT JOIN attendance_students ast ON ac.id = ast.kelas_id
WHERE u.role = 'wali_kelas'
GROUP BY u.id, u.name, u.email, u.phone, ac.nama_kelas, ac.tingkat;
```

### **Query 2: Update Semua Wali Kelas dengan Nomor WA Dummy**
```sql
-- HATI-HATI: Ini contoh, jangan run di production tanpa data real!
UPDATE users 
SET phone = CONCAT('628', LPAD(id, 10, '0'))
WHERE role = 'wali_kelas' AND phone IS NULL;
```

### **Query 3: Export Data Wali Kelas untuk Diisi Nomor WA**
```sql
SELECT 
    id,
    name as 'Nama Wali Kelas',
    email,
    phone as 'Nomor WA (62xxx)',
    (SELECT nama_kelas FROM attendance_classes WHERE id = users.kelas_id) as 'Kelas'
FROM users
WHERE role = 'wali_kelas'
ORDER BY name;
```

Export ke CSV, kasih ke admin sekolah untuk isi nomor WA, lalu import balik.

---

## 🔐 **Security & Privacy Notes**

### **Nomor WA adalah Data Sensitif:**

1. **Enkripsi (Opsional):**
   - Jika mau lebih secure, bisa enkripsi field `phone`
   - Laravel support encrypted casting

2. **Access Control:**
   - Hanya admin dan wali kelas sendiri yang bisa lihat/edit nomor WA
   - Jangan expose di public API tanpa auth

3. **Validation:**
   ```php
   // Di form request
   'phone' => 'nullable|regex:/^62[0-9]{9,13}$/|unique:users,phone'
   ```

4. **Normalization:**
   ```php
   // Helper function untuk normalize phone
   function normalizePhone($phone) {
       $phone = preg_replace('/[^0-9]/', '', $phone);
       if (substr($phone, 0, 1) === '0') {
           $phone = '62' . substr($phone, 1);
       }
       return $phone;
   }
   ```

---

## ✅ **Checklist Lengkap**

- [x] Migration file created: `add_phone_to_users_table.php`
- [x] Model User updated: `phone` added to fillable
- [ ] Run migration: `php artisan migrate`
- [ ] Verify migration: Check table structure
- [ ] Populate data: Add phone numbers for existing wali kelas
- [ ] Test query: Find user by phone
- [ ] Test relasi: User -> Kelas -> Students
- [ ] Ready for chatbot implementation

---

## 🚀 **Next Steps**

Setelah migration & data ready:

1. ✅ **Migration done** → Lanjut implement ChatbotController
2. ✅ **Data populated** → Test Laravel API endpoint
3. ✅ **API tested** → Update WhatsApp Gateway
4. ✅ **Gateway updated** → Import n8n workflow
5. ✅ **All done** → Test end-to-end via WhatsApp

---

**Status:** ⚠️ **PENDING MIGRATION**

**Action Required:**
```bash
php artisan migrate
```

**Last Updated:** 12 Agustus 2026  
**Created By:** Kiro AI Assistant
