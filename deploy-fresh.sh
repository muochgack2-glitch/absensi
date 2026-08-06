#!/bin/bash

# Fresh Deployment Script untuk Absensi
# Target: /www/wwwroot/absensi

set -e  # Stop on error

echo "=== Fresh Deployment Absensi ==="
echo ""

# 1. Backup .env jika ada
echo "1. Backup .env file..."
if [ -f /www/wwwroot/absensi/.env ]; then
    cp /www/wwwroot/absensi/.env /tmp/absensi-env-backup
    echo "   ✓ .env backed up to /tmp/absensi-env-backup"
else
    echo "   ! No .env found to backup"
fi

# 2. Hapus folder lama
echo ""
echo "2. Cleaning old installation..."
cd /www/wwwroot
rm -rf absensi
mkdir -p absensi
echo "   ✓ Clean directory created"

# 3. Clone repository
echo ""
echo "3. Cloning from GitHub..."
cd /www/wwwroot/absensi
git clone https://github.com/muochgack2-glitch/Absensi.git .
echo "   ✓ Repository cloned"

# 4. Restore .env atau copy dari example
echo ""
echo "4. Setting up .env..."
if [ -f /tmp/absensi-env-backup ]; then
    cp /tmp/absensi-env-backup .env
    echo "   ✓ Restored .env from backup"
else
    if [ -f .env.production ]; then
        cp .env.production .env
        echo "   ✓ Created .env from .env.production template"
    else
        cp .env.example .env
        echo "   ✓ Created .env from .env.example"
    fi
    echo "   ⚠ PLEASE VERIFY DATABASE SETTINGS!"
fi

# 5. Set permissions
echo ""
echo "5. Setting permissions..."
chown -R www-data:www-data /www/wwwroot/absensi
chmod -R 755 /www/wwwroot/absensi
chmod -R 775 /www/wwwroot/absensi/storage
chmod -R 775 /www/wwwroot/absensi/bootstrap/cache
echo "   ✓ Permissions set"

# 6. Install PHP dependencies
echo ""
echo "6. Installing PHP dependencies..."
cd /www/wwwroot/absensi
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
echo "   ✓ Composer dependencies installed"

# 7. Install Node dependencies
echo ""
echo "7. Installing Node dependencies..."
npm install
echo "   ✓ NPM dependencies installed"

# 8. Build assets
echo ""
echo "8. Building assets..."
npm run build
echo "   ✓ Assets built"

# 9. Generate app key jika belum ada
echo ""
echo "9. Generating application key..."
php artisan key:generate --force
echo "   ✓ App key generated"

# 10. Run migrations
echo ""
echo "10. Running database migrations..."
php artisan migrate --force
echo "   ✓ Migrations completed"

# 11. Storage link
echo ""
echo "11. Creating storage link..."
php artisan storage:link
echo "   ✓ Storage linked"

# 12. Clear caches
echo ""
echo "12. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "   ✓ Caches cleared"

# 13. Configure Nginx
echo ""
echo "13. Configuring Nginx..."
NGINX_CONF="/www/server/panel/vhost/nginx/absensi.smkpgriblora.sch.id.conf"
if [ -f "$NGINX_CONF" ]; then
    # Update root path
    sed -i 's|root /www/wwwroot/absensi/.*;|root /www/wwwroot/absensi/public;|g' "$NGINX_CONF"
    
    # Test and reload nginx
    nginx -t && systemctl reload nginx
    echo "   ✓ Nginx configured and reloaded"
else
    echo "   ⚠ Nginx config not found at $NGINX_CONF"
fi

# Done
echo ""
echo "=== Deployment Complete ==="
echo ""
echo "Next steps:"
echo "1. Configure .env file (database, app settings)"
echo "2. Run: php artisan db:seed (if needed)"
echo "3. Test the application at https://absensi.smkpgriblora.sch.id"
echo ""
