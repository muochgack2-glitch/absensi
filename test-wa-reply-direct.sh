#!/bin/bash

echo "Testing /wa-reply endpoint directly..."
echo ""

# Test with correct format
curl -X POST https://absensi.smkpgriblora.sch.id/wa-reply \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6285216343400",
    "message": "Test message from curl"
  }' \
  -v

echo ""
echo "---"
echo ""
echo "If you see success:true, the endpoint works!"
