<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppLog;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Menerima webhook ACK status dari gateway WhatsApp (Baileys).
 * Dipanggil oleh gateway setiap kali status centang pesan berubah.
 *
 * ACK levels:
 *   1 = Terkirim ke server WA  (✓ abu)
 *   2 = Diterima di HP penerima (✓✓ abu)
 *   3 = Dibaca penerima          (✓✓ biru)
 *  -1 = Error
 */
class WaAckController extends Controller
{
    public function receive(Request $request)
    {
        // ── Autentikasi sederhana: token sama dengan wa_api_key ──
        $token = $request->bearerToken()
            ?? $request->header('X-API-Key')
            ?? $request->input('secret');

        $apiKey = WhatsAppSetting::get('wa_api_key', '');

        if ($apiKey && $token !== $apiKey) {
            Log::warning('WaAck: unauthorized attempt', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // ── Validasi payload ──
        $validated = $request->validate([
            'message_id' => 'required|string|max:100',
            'ack'        => 'required|integer|min:-1|max:5',
        ]);

        $messageId = $validated['message_id'];
        $ack       = (int) $validated['ack'];

        // ── Cari log berdasarkan message_id ──
        $log = WhatsAppLog::where('message_id', $messageId)->first();

        if (!$log) {
            // Bukan pesan yang kita kirim, atau sudah terlalu lama — abaikan saja
            return response()->json(['success' => true, 'message' => 'Message not tracked']);
        }

        // ── Update ACK (hanya naik) ──
        $log->updateAck($ack);

        Log::info("WaAck: updated message_id={$messageId} ack={$ack} log_id={$log->id}");

        return response()->json([
            'success' => true,
            'log_id'  => $log->id,
            'ack'     => $ack,
        ]);
    }
}