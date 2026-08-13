#!/bin/bash

echo "========================================"
echo "Testing DIRECT /wa-reply endpoint"
echo "This BYPASSES n8n completely"
echo "========================================"
echo ""

# Test 1: Help message
echo "Test 1: Send help message directly"
curl -X POST "https://absensi.smkpgriblora.sch.id/wa-reply?phone=6285216343400&message=🤖%20*MENU%20CHATBOT%20WALI%20KELAS*%0A%0APerintah:%0A1️⃣%20*ringkasan*%20-%20Lihat%20absensi%20hari%20ini%0A2️⃣%20*help*%20-%20Menu%20ini"
echo -e "\n"

sleep 2

# Test 2: Simple message
echo "Test 2: Send simple test message"
curl -X POST "https://absensi.smkpgriblora.sch.id/wa-reply?phone=6285216343400&message=Test%20direct%20message"
echo -e "\n"

echo "========================================"
echo "If these work, the problem is in n8n workflow"
echo "If these don't work, the problem is in server.js or WA Gateway"
echo "========================================"
