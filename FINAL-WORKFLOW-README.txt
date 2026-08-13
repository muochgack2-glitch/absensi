================================================================================
N8N CHATBOT FINAL DYNAMIC WORKFLOW
================================================================================

FILE: n8n-chatbot-FINAL-DYNAMIC.json

INI ADALAH EXACT COPY dari workflow yang SUDAH WORKING (wa-chatbot-v2)
dengan HANYA 1 PERUBAHAN: Phone parameter dari hardcoded ke dynamic

================================================================================
PERUBAHAN DARI WORKING VERSION:
================================================================================

WORKING VERSION (wa-chatbot-v2):
- Node "Send to WhatsApp"
- Phone: HARDCODED "6285216343400"
- Message: DYNAMIC "={{ $json.message }}"

FINAL DYNAMIC VERSION (wa-chatbot-final):
- Node "Send to WhatsApp"
- Phone: DYNAMIC "={{ $json.phone }}"  ← HANYA INI YANG BERUBAH!
- Message: DYNAMIC "={{ $json.message }}"

================================================================================
IMPORT INSTRUCTIONS:
================================================================================

1. Buka https://n8n.dmcenter.my.id

2. Import workflow:
   - Klik "Workflows" → "+ Add Workflow" → "Import from File"
   - File: /www/wwwroot/absensi/n8n-chatbot-FINAL-DYNAMIC.json
   - Import

3. Aktifkan workflow:
   - Switch ke mode "Production" (kanan atas)
   - Toggle "Active" ON (hijau)
   - Webhook path akan aktif: /webhook/wa-chatbot-final

4. Test dengan curl:
   cd /www/wwwroot/absensi
   bash test-wa-chatbot-final.sh

================================================================================
EXPECTED RESULT:
================================================================================

curl -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot-final \
-H "Content-Type: application/json" \
-d '{"body":{"from":"85216343400@lid","message":"help"}}'

OUTPUT:
{"success":true}

DAN WhatsApp harus menerima pesan help menu!

================================================================================
WHY THIS SHOULD WORK:
================================================================================

1. ✅ Workflow structure EXACT sama dengan yang working
2. ✅ Node versions sama (typeVersion 3.3 untuk Set nodes)
3. ✅ Webhook responseMode: "lastNode" (sudah terbukti work)
4. ✅ Respond node format: {{ { "success": true } }} (sudah terbukti work)
5. ✅ bodyParameters dengan specifyBody: "keypair" (sudah terbukti work di v2.34.5)
6. ✅ Expression untuk phone: {{ $json.phone }} (simple, tidak kompleks)

Satu-satunya perubahan adalah phone parameter dari string literal ke expression.
Jika ini tidak work, berarti ada issue dengan expression evaluation di node 
"Send to WhatsApp" untuk parameter phone.

================================================================================
TROUBLESHOOTING:
================================================================================

Jika masih tidak work setelah import:

1. CEK N8N EXECUTION LOGS:
   - Buka n8n dashboard
   - Klik "Executions"
   - Cari execution untuk workflow "WhatsApp Chatbot - Final Dynamic"
   - Klik untuk lihat detail
   - Cek node mana yang error (merah)

2. CEK PHONE VALUE DI SETIAP NODE:
   - Di execution detail, klik setiap node
   - Cek output data
   - Pastikan phone value ter-pass dengan benar dari:
     Parse Input → Set Help/Ringkasan/Unknown → Send to WhatsApp

3. BANDINGKAN DENGAN WORKING VERSION:
   - Buka 2 workflows side-by-side di n8n editor
   - "WhatsApp Chatbot - Hardcoded Multi HTTP" (working)
   - "WhatsApp Chatbot - Final Dynamic" (new)
   - Bandingkan node "Send to WhatsApp"
   - Pastikan semua settings EXACT sama kecuali phone value

4. TEST MANUAL DI N8N EDITOR:
   - Buka workflow "WhatsApp Chatbot - Final Dynamic"
   - Klik node "Webhook"
   - Klik "Listen for Test Event"
   - Jalankan curl test
   - Lihat data flow dari node ke node
   - Cek apakah phone value muncul dengan benar

================================================================================
NEXT STEPS SETELAH SUKSES:
================================================================================

1. Update whatsapp-server/server.js:
   Ubah webhook URL dari:
   'http://localhost:5678/webhook/wa-chatbot-v2'
   
   Menjadi:
   'http://localhost:5678/webhook/wa-chatbot-final'

2. Restart WhatsApp Gateway:
   pm2 restart whatsapp-absensi

3. Test end-to-end dari WhatsApp:
   Kirim pesan "help" dari HP
   Kirim pesan "ringkasan"

4. Deactivate workflow lama:
   - "WhatsApp Chatbot - Hardcoded Multi HTTP"
   - Toggle Active OFF

================================================================================
