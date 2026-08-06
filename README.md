# 📱 Sistem Absensi Siswa SMK PGRI Blora

Sistem absensi berbasis web menggunakan QR Code untuk SMK PGRI Blora. Dibangun dengan Laravel 11, Vite, dan Bootstrap 5.

## ✨ Fitur Utama

- 📷 **Scan QR Code** - Absensi cepat dengan kamera smartphone
- 📊 **Dashboard Real-time** - Monitoring kehadiran siswa live
- 📑 **Export Laporan** - Download laporan Excel/PDF
- 👥 **Manajemen Kelas** - Kelola data kelas dan siswa
- 📱 **WhatsApp Gateway** - Notifikasi otomatis ke orang tua (opsional)
- 🎨 **Responsive Design** - Mobile-friendly interface

## 🚀 Quick Deploy ke Production

### Langkah 1: Buat Database MySQL

Login ke aaPanel → Database → Add Database:
- **Name**: `absensi_db`
- **User**: `absensi_db`  
- **Password**: `password`

### Langkah 2: Deploy Aplikasi

SSH ke server dan jalankan ONE command ini:

```bash
cd /tmp && wget -O deploy.sh https://raw.githubusercontent.com/muochgack2-glitch/Absensi/master/quick-deploy.sh && chmod +x deploy.sh && ./deploy.sh
```

### Langkah 3: Setup Admin

```bash
cd /www/wwwroot/absensi
php artisan db:seed --class=AdminUserSeeder
```

### Langkah 4: Test

Buka: `https://absensi.smkpgriblora.sch.id`

**Login:**
- Email: `admin@smkpgriblora.sch.id`
- Password: `admin123`

✅ **Done!**

📖 Panduan lengkap: Lihat file [`START-HERE.txt`](START-HERE.txt)

## 🛠️ Development Setup (Local)

### Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Installation

```bash
# Clone repository
git clone https://github.com/muochgack2-glitch/Absensi.git
cd Absensi

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database di .env
DB_CONNECTION=mysql
DB_DATABASE=absensi_db
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed data
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=AttendanceClassSeeder
php artisan db:seed --class=AttendanceStudentSeeder

# Build assets
npm run build

# Start server
php artisan serve
```

Buka: `http://localhost:8000`

## 📁 Struktur Folder

```
absensi/
├── app/
│   ├── Http/Controllers/
│   │   ├── AttendanceQRController.php
│   │   ├── AttendanceScanController.php
│   │   ├── AttendanceDashboardController.php
│   │   └── ...
│   ├── Models/
│   │   ├── AttendanceClass.php
│   │   ├── AttendanceStudent.php
│   │   ├── AttendanceRecord.php
│   │   └── ...
├── resources/
│   ├── js/
│   │   ├── sidebar.js      # Sidebar navigation
│   │   ├── navbar.js       # Top navbar
│   │   └── app.js
│   ├── views/
│   │   ├── attendance/
│   │   │   ├── scanner.blade.php
│   │   │   ├── dashboard.blade.php
│   │   │   └── ...
│   └── css/
├── public/
│   ├── build/              # Compiled assets (Vite)
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
└── ...
```

## 🔧 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5, Vanilla JavaScript
- **Build**: Vite
- **Database**: MySQL
- **QR Code**: jsQR, qrcode-generator
- **Server**: Nginx + PHP-FPM

## 📝 Deployment Files

- [`START-HERE.txt`](START-HERE.txt) - Panduan deployment step-by-step
- [`quick-deploy.sh`](quick-deploy.sh) - Script auto deployment
- [`deploy-fresh.sh`](deploy-fresh.sh) - Full deployment script dengan log
- [`COPY-PASTE-COMMANDS.txt`](COPY-PASTE-COMMANDS.txt) - Manual commands
- [`create-database.sh`](create-database.sh) - Script buat database
- [`.env.production`](.env.production) - Template .env production

## 🐛 Troubleshooting

**Error 500 / Blank Page:**
```bash
tail -f /www/wwwroot/absensi/storage/logs/laravel.log
chmod -R 775 storage bootstrap/cache
```

**Database Connection Error:**
```bash
# Cek .env
nano /www/wwwroot/absensi/.env

# Test database
mysql -u absensi_db -p absensi_db
```

**Assets 404:**
```bash
cd /www/wwwroot/absensi
npm run build
php artisan view:clear
```

## 📧 Support

Ada pertanyaan? Hubungi admin SMK PGRI Blora.

## 📄 License

Project ini menggunakan [MIT license](https://opensource.org/licenses/MIT).
