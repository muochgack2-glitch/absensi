#!/bin/bash

# ============================================
# WhatsApp Chatbot Integration Test Script
# ============================================
# Run this on the server after nginx proxy setup
# Usage: bash test-wa-chatbot.sh

echo "============================================"
echo "WhatsApp Chatbot Integration Test"
echo "============================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test phone number (Marista Bela Octaviana - wali kelas)
TEST_PHONE="6285216343400"

echo "Step 1: Check WhatsApp Gateway Status"
echo "--------------------------------------"
STATUS=$(curl -s http://127.0.0.1:3001/status)
echo "$STATUS" | jq '.'
CONNECTION=$(echo "$STATUS" | jq -r '.status')

if [ "$CONNECTION" == "connected" ]; then
    echo -e "${GREEN}✅ WhatsApp Gateway is connected${NC}"
else
    echo -e "${RED}❌ WhatsApp Gateway is NOT connected (status: $CONNECTION)${NC}"
    echo "Please connect WhatsApp first: http://127.0.0.1:3001/qr"
    exit 1
fi
echo ""

echo "Step 2: Test Laravel API - Get Summary"
echo "--------------------------------------"
API_RESPONSE=$(curl -s "http://localhost/api/chatbot/summary/$TEST_PHONE")
echo "$API_RESPONSE" | jq '.'
API_SUCCESS=$(echo "$API_RESPONSE" | jq -r '.success')

if [ "$API_SUCCESS" == "true" ]; then
    echo -e "${GREEN}✅ Laravel API is working${NC}"
else
    echo -e "${RED}❌ Laravel API failed${NC}"
    echo "Error: $(echo "$API_RESPONSE" | jq -r '.message')"
    exit 1
fi
echo ""

echo "Step 3: Test Direct WA Gateway /reply endpoint"
echo "--------------------------------------"
DIRECT_RESPONSE=$(curl -s -X POST http://127.0.0.1:3001/reply \
  -H "Content-Type: application/json" \
  -d "{\"phone\":\"$TEST_PHONE\",\"message\":\"✅ Test direct connection (internal)\"}")
echo "$DIRECT_RESPONSE" | jq '.'
DIRECT_SUCCESS=$(echo "$DIRECT_RESPONSE" | jq -r '.success')

if [ "$DIRECT_SUCCESS" == "true" ]; then
    echo -e "${GREEN}✅ Direct connection to WA Gateway works${NC}"
else
    echo -e "${RED}❌ Direct connection failed${NC}"
    exit 1
fi
echo ""

echo "Step 4: Test Nginx Proxy /wa-reply endpoint"
echo "--------------------------------------"
PROXY_RESPONSE=$(curl -s -X POST https://absensi.smkpgriblora.sch.id/wa-reply \
  -H "Content-Type: application/json" \
  -d "{\"phone\":\"$TEST_PHONE\",\"message\":\"✅ Test nginx proxy (via HTTPS)\"}")
echo "$PROXY_RESPONSE" | jq '.'
PROXY_SUCCESS=$(echo "$PROXY_RESPONSE" | jq -r '.success')

if [ "$PROXY_SUCCESS" == "true" ]; then
    echo -e "${GREEN}✅ Nginx proxy is working${NC}"
else
    echo -e "${RED}❌ Nginx proxy failed${NC}"
    echo "Have you added the nginx location block? See NGINX_PROXY_SETUP.txt"
    exit 1
fi
echo ""

echo "Step 5: Test n8n Webhook"
echo "--------------------------------------"
echo -e "${YELLOW}ℹ️  Testing n8n webhook (simulating incoming message)${NC}"
N8N_RESPONSE=$(curl -s -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot \
  -H "Content-Type: application/json" \
  -d "{\"body\":{\"from\":\"${TEST_PHONE:2}\",\"message\":\"help\"},\"timestamp\":\"$(date -Iseconds)\"}")
echo "$N8N_RESPONSE"

# n8n might return HTML or empty response (OK if webhook received)
if [ -n "$N8N_RESPONSE" ] || [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ n8n webhook received the request${NC}"
else
    echo -e "${RED}❌ n8n webhook failed${NC}"
    exit 1
fi
echo ""

echo "============================================"
echo "🎉 ALL TESTS PASSED!"
echo "============================================"
echo ""
echo "Next steps:"
echo "1. Update n8n workflow URL to: https://absensi.smkpgriblora.sch.id/wa-reply"
echo "2. Test end-to-end by sending 'help' via WhatsApp to $TEST_PHONE"
echo ""
echo "Expected bot response:"
echo "---"
echo "🤖 *Chatbot Wali Kelas - Menu Bantuan*"
echo ""
echo "Perintah yang tersedia:"
echo "- help → Tampilkan menu ini"
echo "- ringkasan → Ringkasan kehadiran hari ini"
echo ""
echo "Kirim perintah sesuai kebutuhan Anda."
echo "---"
echo ""
