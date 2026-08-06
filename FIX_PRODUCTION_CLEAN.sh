#!/bin/bash
# ============================================
# FIX PRODUCTION: Clean Clone dari GitHub
# ============================================
# Author: Kiro AI Assistant
# Date: 2026-08-06
# Purpose: Fresh clone Absensi yang sudah clean (tanpa WA Gateway)

echo "======================================"
echo "STEP 1: Backup .env yang ada"
echo "======================================"
cd /www/wwwroot/absensi
cp .env /root/.env.absensi.backup
echo "✓ .env backed up to /root/.env.absensi.backup"

echo ""
echo "======================================"
echo "STEP 2: Hapus semua file di folder"
echo "======================================"
cd /www/wwwroot
rm -rf absensi/*
rm -rf absensi/.git
rm -rf absensi/.env*
echo "✓ Folder absensi dikosongkan"

echo ""
echo "======================================"
echo "STEP 3: Clone fresh dari GitHub"
echo "======================================"
cd /www/wwwroot/absensi
git clone https://github.com/muochgack2-glitch/Absensi.git .
echo "✓ Clone completed"

echo ""
echo "======================================"
echo "STEP 4: Restore .env"
echo "======================================"
cp /root/.env.absensi.backup .env
echo "✓ .env restored"

echo ""
echo "======================================"
echo "STEP 5: Install Dependencies"
echo "======================================"
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
echo "✓ Composer install completed"

echo ""
echo "======================================"
echo "STEP 6: Setup Storage & Permissions"
echo "======================================"
mkdir -p storage/app/public
mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

chown -R www-data:www-data /www/wwwroot/absensi
chmod -R 755 /www/wwwroot/absensi
chmod -R 775 storage bootstrap/cache
echo "✓ Storage structure created"

echo ""
echo "======================================"
echo "STEP 7: Laravel Setup"
echo "======================================"
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "✓ Laravel caches cleared"

echo ""
echo "======================================"
echo "STEP 8: Restart PHP-FPM"
echo "======================================"
systemctl restart php-fpm-83
echo "✓ PHP-FPM restarted"

echo ""
echo "======================================"
echo "STEP 9: Verify Struktur Folder"
echo "======================================"
echo "Cek apakah masih ada whatsapp-server:"
ls -la | grep whatsapp || echo "✓ CLEAN - Tidak ada folder whatsapp-server"

echo ""
echo "Cek git commit:"
git log --oneline -3

echo ""
echo "Cek routes:"
php artisan route:list | grep -i whatsapp || echo "✓ CLEAN - Tidak ada route WhatsApp"

echo ""
echo "======================================"
echo "✅ DEPLOYMENT COMPLETED"
echo "======================================"
echo "Silakan test di browser:"
echo "https://absensi.smkpgriblora.sch.id"
echo ""
echo "Login default:"
echo "Email: admin@absensi.test"
echo "Password: password"
