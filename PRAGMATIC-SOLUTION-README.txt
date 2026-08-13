================================================================================
PRAGMATIC SOLUTION - PHONE FROM SERVER.JS
================================================================================

STRATEGY: Instead of fighting n8n expression bugs, let server.js do the work!

================================================================================
HOW IT WORKS:
================================================================================

1. WHATSAPP MESSAGE ARRIVES
   User sends: "help"
   From: 85216343400@lid

2. SERVER.JS PROCESSES
   - Extract phone: "85216343400"
   - Add prefix: "6285216343400"
   - Forward to n8n WITH phone at root level:
   
   {
     "body": {
       "from": "85216343400@lid",
       "message": "help"
     },
     "phone": "6285216343400",  ← ADD THIS!
     "timestamp": "..."
   }

3. N8N WORKFLOW
   - Parse Input gets phone from $json.phone (root level, NOT from body)
   - Rest of workflow same as before
   - Send to WhatsApp uses query params (already working)

4. WHATSAPP GATEWAY SENDS
   - POST /wa-reply?phone=6285216343400&message=...
   - server.js sends to correct user

================================================================================
KEY CHANGES:
================================================================================

FILE 1: whatsapp-server/server.js
CHANGE: Add phone extraction and formatting

BEFORE:
await axios.post(url, {
  body: {
    from: from,
    message: text
  },
  timestamp: new Date().toISOString()
});

AFTER:
const formattedPhone = '62' + from;  // Add 62 prefix
await axios.post(url, {
  body: {
    from: from + '@lid',
    message: text
  },
  phone: formattedPhone,  ← ADD THIS!
  timestamp: new Date().toISOString()
});

FILE 2: n8n workflow (n8n-chatbot-USE-ROOT-PHONE.json)
CHANGE: Parse Input gets phone from root

BEFORE (broken):
"value": "={{ '62' + $json.body.from.split('@')[0] }}"  ← Complex expression

AFTER (simple):
"value": "={{ $json.phone }}"  ← Simple, phone already formatted!

================================================================================
WHY THIS WORKS:
================================================================================

1. ✅ Phone formatting done in server.js (JavaScript, reliable)
2. ✅ N8n just reads simple $json.phone (no complex expressions)
3. ✅ Query params for Send to WhatsApp (already tested, works)
4. ✅ No bodyParameters expressions (avoid the bug)

================================================================================
TESTING:
================================================================================

# 1. Update server.js on server
cd /www/wwwroot/absensi
git pull

# 2. Restart WhatsApp Gateway
pm2 restart whatsapp-absensi

# 3. Import new workflow to n8n
#    File: n8n-chatbot-USE-ROOT-PHONE.json
#    Path: /webhook/wa-chatbot-v2 (SAME as old working workflow)
#    Mode: Production + Active ON

# 4. Test by sending WhatsApp message
Send "help" from phone 085216343400

# Or test with curl (simulating server.js):
curl -X POST https://n8n.dmcenter.my.id/webhook/wa-chatbot-v2 \
-H "Content-Type: application/json" \
-d '{"body":{"from":"85216343400@lid","message":"help"},"phone":"6285216343400"}'

EXPECTED:
{"success":true}
WhatsApp receives help menu!

================================================================================
ADVANTAGES:
================================================================================

1. NO complex expressions in n8n
2. Phone formatting centralized in server.js
3. Works for ALL users (truly dynamic)
4. Uses query params (proven working)
5. Minimal changes to existing working workflow

================================================================================
IF YOU WANT TO REPLACE HARDCODED WORKFLOW:
================================================================================

The workflow path is SAME: /webhook/wa-chatbot-v2

Option A: Delete old workflow, import new one
Option B: Edit existing workflow:
  - Change Parse Input phone from hardcoded to {{ $json.phone }}
  - Change Send to WhatsApp to use query params

================================================================================
