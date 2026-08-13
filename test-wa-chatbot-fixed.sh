#!/bin/bash

# Test script untuk n8n chatbot FIXED PHONE workflow
# Path: /webhook/wa-chatbot-fixed
# FIX: Use $input.item.json instead of $json for phone and message

echo "========================================"
echo "Testing n8n FIXED PHONE Chatbot"
echo "========================================"
echo ""

N8N_URL="https://n8n.dmcenter.my.id/webhook/wa-chatbot-fixed"
TEST_PHONE="85216343400@lid"

# Test 1: Help command
echo "Test 1: Help command"
curl -X POST $N8N_URL \
-H "Content-Type: application/json" \
-d "{\"body\":{\"from\":\"$TEST_PHONE\",\"message\":\"help\"}}"
echo -e "\n"

sleep 2

# Test 2: Ringkasan command
echo "Test 2: Ringkasan command"
curl -X POST $N8N_URL \
-H "Content-Type: application/json" \
-d "{\"body\":{\"from\":\"$TEST_PHONE\",\"message\":\"ringkasan\"}}"
echo -e "\n"

sleep 2

# Test 3: Unknown command
echo "Test 3: Unknown command"
curl -X POST $N8N_URL \
-H "Content-Type: application/json" \
-d "{\"body\":{\"from\":\"$TEST_PHONE\",\"message\":\"test123\"}}"
echo -e "\n"

echo "========================================"
echo "All tests completed!"
echo "Expected: WhatsApp 6285216343400 receives messages"
echo "========================================"
