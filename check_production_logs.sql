-- Jalankan query ini di production database

-- 1. Check recent WhatsApp logs with errors
SELECT id, phone, status, type, error_message, created_at 
FROM whatsapp_logs 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY created_at DESC 
LIMIT 10;

-- 2. Check notification logs
SELECT id, student_id, action, message, status, response, created_at
FROM attendance_logs
WHERE action = 'notification'
AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY created_at DESC
LIMIT 10;

-- 3. Check failover settings
SELECT `key`, `value` 
FROM whatsapp_settings 
WHERE `key` IN ('wa_server_url', 'wa_server_url_backup', 'wa_failover_enabled', 'wa_failover_timeout');

-- 4. Check notification settings
SELECT `key`, `value`
FROM attendance_settings
WHERE `key` IN ('enable_parent_notification', 'notify_all_checkin', 'notify_checkout', 'include_photo_in_notification');
