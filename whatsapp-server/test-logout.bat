@echo off
REM WhatsApp Gateway - Test Logout & Auto-Reconnect (Windows)
REM Usage: test-logout.bat

echo ==========================================
echo WhatsApp Gateway - Logout Test
echo ==========================================
echo.

REM Configuration
set API_URL=http://localhost:3000

REM Step 1: Check initial status
echo Step 1: Check initial status
curl -s "%API_URL%/status"
echo.
timeout /t 2 /nobreak >nul

REM Step 2: Trigger logout
echo Step 2: Triggering logout...
curl -s -X POST "%API_URL%/logout"
echo.
timeout /t 3 /nobreak >nul

REM Step 3: Monitor status changes
echo Step 3: Monitoring status changes...
for /L %%i in (1,1,10) do (
    echo Check #%%i (waiting for QR generation)...
    curl -s "%API_URL%/status"
    echo.
    timeout /t 3 /nobreak >nul
)

echo.
echo Test completed. Check the output above.
echo If status is "qr" and qrAvailable is true, the test PASSED.
echo.
echo To view QR code, open: http://localhost:3000/qr
echo Or check dashboard: http://your-domain.com/whatsapp
echo.
pause
