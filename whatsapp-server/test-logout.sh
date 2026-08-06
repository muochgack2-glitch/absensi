#!/bin/bash

# WhatsApp Gateway - Test Logout & Auto-Reconnect
# Usage: ./test-logout.sh

echo "=========================================="
echo "WhatsApp Gateway - Logout Test"
echo "=========================================="
echo ""

# Configuration
API_URL="http://localhost:3000"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to check status
check_status() {
    echo -e "${YELLOW}Checking status...${NC}"
    curl -s "$API_URL/status" | jq '.'
    echo ""
}

# Function to get QR
check_qr() {
    echo -e "${YELLOW}Checking QR code availability...${NC}"
    curl -s "$API_URL/qr" | jq '.success, .message'
    echo ""
}

# Step 1: Check initial status
echo "Step 1: Check initial status"
check_status
sleep 2

# Step 2: Trigger logout
echo "Step 2: Triggering logout..."
LOGOUT_RESPONSE=$(curl -s -X POST "$API_URL/logout")
echo "$LOGOUT_RESPONSE" | jq '.'
echo ""

if [ "$(echo "$LOGOUT_RESPONSE" | jq -r '.success')" = "true" ]; then
    echo -e "${GREEN}✓ Logout request successful${NC}"
else
    echo -e "${RED}✗ Logout request failed${NC}"
    exit 1
fi

sleep 3

# Step 3: Monitor status changes
echo "Step 3: Monitoring status changes..."
for i in {1..10}; do
    echo -e "${YELLOW}Check #$i (waiting for QR generation)...${NC}"
    STATUS=$(curl -s "$API_URL/status")
    
    CONNECTION_STATE=$(echo "$STATUS" | jq -r '.status')
    QR_AVAILABLE=$(echo "$STATUS" | jq -r '.qrAvailable')
    RECONNECT_ATTEMPTS=$(echo "$STATUS" | jq -r '.reconnectAttempts')
    
    echo "  Status: $CONNECTION_STATE"
    echo "  QR Available: $QR_AVAILABLE"
    echo "  Reconnect Attempts: $RECONNECT_ATTEMPTS"
    echo ""
    
    if [ "$CONNECTION_STATE" = "qr" ] && [ "$QR_AVAILABLE" = "true" ]; then
        echo -e "${GREEN}✓ SUCCESS! QR code generated automatically${NC}"
        echo ""
        echo "Step 4: Fetching QR code..."
        check_qr
        echo -e "${GREEN}=========================================="
        echo "Test PASSED!"
        echo "QR code is ready to scan"
        echo "==========================================${NC}"
        exit 0
    fi
    
    if [ "$RECONNECT_ATTEMPTS" -ge 5 ]; then
        echo -e "${RED}✗ FAILED! Max reconnect attempts reached${NC}"
        exit 1
    fi
    
    sleep 3
done

echo -e "${RED}✗ TIMEOUT! QR code not generated within 30 seconds${NC}"
echo "Please check PM2 logs: pm2 logs spmb-wa-gateway"
exit 1
