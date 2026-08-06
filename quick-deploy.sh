#!/bin/bash
# Quick Deploy - Copy paste ke terminal production

# Backup .env jika ada
[ -f /www/wwwroot/absensi/.env ] && cp /www/wwwroot/absensi/.env /tmp/absensi-env-backup

# Hapus dan buat folder baru
cd /www/wwwroot && rm -rf absensi && mkdir -p absensi

# Clone dari GitHub
cd /www/wwwroot/absensi && git clone https://github.com/muochgack2-glitch/Absensi.git .

# Setup .env
if [ -f /tmp/absensi-env-backup ]; then
    cp /tmp/absensi-env-backup .env
else
    [ -f .env.production ] && cp .env.production .env || cp .env.example .env
    echo "⚠ Verify database settings in .env"
fi

# Permissions
chown -R www-data:www-data /www/wwwroot/absensi
chmod -R 755 /www/wwwroot/absensi
chmod -R 775 storage bootstrap/cache

# Install dependencies
composer install --ignore-platform-reqs --no-dev --optimize-autoloader
npm install && npm run build

# Laravel setup
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Fix Nginx (backup dulu config lama)
NGINX_CONF="/www/server/panel/vhost/nginx/absensi.smkpgriblora.sch.id.conf"
[ -f "$NGINX_CONF" ] && cp "$NGINX_CONF" "$NGINX_CONF.backup"
sed -i 's|root /www/wwwroot/absensi/.*;|root /www/wwwroot/absensi/public;|g' "$NGINX_CONF"
nginx -t && systemctl reload nginx

echo "✓ Deployment selesai! Test: https://absensi.smkpgriblora.sch.id"
