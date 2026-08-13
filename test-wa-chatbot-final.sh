#!/bin/bash

# Test script untuk n8n chatbot FINAL DYNAMIC workflow
# Path: /webhook/wa-chatbot-final
# THIS IS EXACT COPY of working workflow with ONLY phone changed to dynamic

echo "========================================"
echo "Testing n8n FINAL Dynamic Chatbot"
echo "========================================"
echo ""

N8N_URL="https://n8n.dmcenter.my.id/webhook/wa-chatbot-final"
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
echo "========================================"
