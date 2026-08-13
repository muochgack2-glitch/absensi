#!/bin/bash

echo "======================================"
echo "Test 1: POST with query parameters"
echo "======================================"
curl -X POST "https://absensi.smkpgriblora.sch.id/wa-reply?phone=6285216343400&message=Test%20dari%20query%20params" -v

echo ""
echo ""
echo "======================================"
echo "Test 2: GET with query parameters"
echo "======================================"
curl -X GET "https://absensi.smkpgriblora.sch.id/wa-reply?phone=6285216343400&message=Test%20GET%20method" -v

echo ""
echo ""
echo "======================================"
echo "Test 3: POST with JSON body (reference)"
echo "======================================"
curl -X POST https://absensi.smkpgriblora.sch.id/wa-reply \
  -H "Content-Type: application/json" \
  -d '{"phone":"6285216343400","message":"Test dari JSON body"}' -v
