#!/bin/bash

# Setup Script untuk n8n Chatbot Integration
# SMK PGRI Blora - Attendance System
# Date: 12 Agustus 2026

echo "=================================================="
echo "🤖 Setup n8n Chatbot Integration"
echo "=================================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Base paths
LARAVEL_PATH="/www/wwwroot/absensi"
WA_SERVER_PATH="/www/wwwroot/absensi/whatsapp-server"

echo "📋 Checklist Setup:"
echo "  - Laravel: $LARAVEL_PATH"
echo "  - WA Gateway: $WA_SERVER_PATH"
echo ""

# Step 1: Install axios di WhatsApp Gateway
echo -e "${YELLOW}[1/5]${NC} Installing axios di WhatsApp Gateway..."
cd "$WA_SERVER_PATH" || exit 1

if npm list axios &>/dev/null; then
    echo -e "${GREEN}✓${NC} axios already installed"
else
    echo "Installing axios..."
    npm install axios
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} axios installed successfully"
    else
        echo -e "${RED}✗${NC} Failed to install axios"
        exit 1
    fi
fi
echo ""

# Step 2: Run Laravel Migration
echo -e "${YELLOW}[2/5]${NC} Running Laravel migration (add phone field)..."
cd "$LARAVEL_PATH" || exit 1

php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Migration completed"
else
    echo -e "${RED}✗${NC} Migration failed"
    echo "Please check your database connection and try again"
    exit 1
fi
echo ""

# Step 3: Verify Migration
echo -e "${YELLOW}[3/5]${NC} Verifying migration..."
php artisan tinker --execute="echo Schema::hasColumn('users', 'phone') ? 'true' : 'false';" > /tmp/migration_check.txt 2>&1
if grep -q "true" /tmp/migration_check.txt; then
    echo -e "${GREEN}✓${NC} Field 'phone' exists in users table"
else
    echo -e "${RED}✗${NC} Field 'phone' not found in users table"
    exit 1
fi
rm -f /tmp/migration_check.txt
echo ""

# Step 4: Clear Laravel cache
echo -e "${YELLOW}[4/5]${NC} Clearing Laravel cache..."
cd "$LARAVEL_PATH" || exit 1
php artisan config:clear
php artisan route:clear
php artisan cache:clear
echo -e "${GREEN}✓${NC} Cache cleared"
echo ""

# Step 5: Restart WhatsApp Gateway
echo -e "${YELLOW}[5/5]${NC} Restarting WhatsApp Gateway..."
if command -v pm2 &> /dev/null; then
    pm2 restart whatsapp-gateway-absensi
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} WhatsApp Gateway restarted"
    else
        echo -e "${RED}✗${NC} Failed to restart WhatsApp Gateway"
        echo "Try manually: pm2 restart whatsapp-gateway-absensi"
    fi
else
    echo -e "${YELLOW}!${NC} PM2 not found. Please restart gateway manually"
fi
echo ""

# Summary
echo "=================================================="
echo -e "${GREEN}✓ Setup Completed!${NC}"
echo "=================================================="
echo ""
echo "📝 Next Steps:"
echo ""
echo "1. Import workflow ke n8n:"
echo "   - Open: https://n8n.dmcenter.my.id"
echo "   - Import file: n8n-chatbot-walikelas-workflow-v2.json"
echo "   - Activate workflow (toggle ON)"
echo ""
echo "2. Update nomor WA wali kelas:"
echo "   cd $LARAVEL_PATH"
echo '   php artisan tinker'
echo '   $user = User::where("email", "rina@smkpgriblora.sch.id")->first();'
echo '   $user->phone = "6281234567890";'
echo '   $user->save();'
echo ""
echo "3. Test API:"
echo "   curl https://absensi.smkpgriblora.sch.id/api/chatbot/summary/6281234567890"
echo ""
echo "4. Test via WhatsApp:"
echo '   Kirim pesan: "ringkasan kehadiran hari ini"'
echo ""
echo "=================================================="
echo "📚 Documentation: N8N_CHATBOT_SETUP.md"
echo "=================================================="
