#!/bin/bash
# ============================================
# PRODUCTION SETUP - Fresh Clone (Clean)
# ============================================
# Path: /www/wwwroot/absensi
# Date: 2026-08-06

echo "======================================"
echo "STEP 1: Verify Clean Clone"
echo "======================================"
cd /www/wwwroot/absensi

# Check git status
echo "Git commit:"
git log --oneline -1

# Check for whatsapp-server (should NOT exist)
echo ""
echo "Check for whatsapp-server folder:"
ls -la | grep whatsapp || echo "✓ CLEAN - No whatsapp-server folder"

echo ""
echo "======================================"
echo "STEP 2: Fix Ownership First"
echo "======================================"
# Fix ownership BEFORE chmod
chown -R www-data:www-data /www/wwwroot/absensi
echo "✓ Ownership set to www-data:www-data"

echo ""
echo "======================================"
echo "STEP 3: Create Storage Structure"
echo "======================================"
# Create storage directories if not exist
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set ownership again after creating dirs
chown -R www-data:www-data storage bootstrap/cache
echo "✓ Storage structure created"

echo ""
echo "======================================"
echo "STEP 4: Set Permissions"
echo "======================================"
# Set base permissions
chmod -R 755 /www/wwwroot/absensi

# Set storage and cache to writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Make artisan executable
chmod +x artisan

echo "✓ Permissions set correctly"

echo ""
echo "======================================"
echo "STEP 5: Copy/Setup .env"
echo "======================================"
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✓ .env created from .env.example"
else
    echo "✓ .env already exists"
fi

# Show current .env database config
echo ""
echo "Current .env database config:"
cat .env | grep -E "DB_CONNECTION|DB_DATABASE|DB_USERNAME|DB_PASSWORD"

echo ""
echo "======================================"
echo "STEP 6: Install Dependencies"
echo "======================================"
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
echo "✓ Composer install completed"

echo ""
echo "======================================"
echo "STEP 7: Laravel Setup"
echo "======================================"
# Generate key if not exist
php artisan key:generate --force

# Create storage link
php artisan storage:link

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✓ Laravel setup completed"

echo ""
echo "======================================"
echo "STEP 8: Database Migration"
echo "======================================"
echo "Running migrations..."
php artisan migrate --force

echo ""
echo "======================================"
echo "STEP 9: Seed Admin User (Optional)"
echo "======================================"
echo "Do you want to seed admin user? (Skip if already exists)"
echo "Run manually: php artisan db:seed --class=AdminUserSeeder"

echo ""
echo "======================================"
echo "STEP 10: Final Verification"
echo "======================================"
# Check permissions
echo "Storage permissions:"
ls -la storage/ | head -5

echo ""
echo "Bootstrap cache permissions:"
ls -la bootstrap/cache/ | head -3

echo ""
echo "Check Laravel:"
php artisan --version

echo ""
echo "Check routes (should NOT have whatsapp):"
php artisan route:list | grep -i whatsapp || echo "✓ CLEAN - No WhatsApp routes"

echo ""
echo "======================================"
echo "STEP 11: Restart PHP-FPM"
echo "======================================"
systemctl restart php-fpm-83
echo "✓ PHP-FPM restarted"

echo ""
echo "======================================"
echo "✅ PRODUCTION SETUP COMPLETED"
echo "======================================"
echo ""
echo "Website: https://absensi.smkpgriblora.sch.id"
echo ""
echo "Default Login:"
echo "Email: admin@absensi.test"
echo "Password: password"
echo ""
echo "Next steps:"
echo "1. Test login di browser"
echo "2. Seed admin jika perlu: php artisan db:seed --class=AdminUserSeeder"
echo "3. Test QR scanner functionality"
echo "4. Verify tidak ada error di: tail -f storage/logs/laravel.log"
