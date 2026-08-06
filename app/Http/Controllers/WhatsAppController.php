<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppSetting;
use App\Models\AttendanceStudent;
use App\Models\AttendanceClass;
use App\Models\AttendanceSetting;
use App\Services\AttendanceWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected AttendanceWhatsAppService $whatsappService;

    public function __construct(AttendanceWhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Dashboard WhatsApp Gateway
     */
    public function index()
    {
        $status = $this->whatsappService->getStatus();
        $statistics = $this->whatsappService->getStatistics();
        
        $recentLogs = WhatsAppLog::with(['student', 'template', 'sender'])
            ->latest()
            ->limit(10)
            ->get();

        $activeServerUrl = $this->whatsappService->getCurrentServerUrl();

        return view('whatsapp.index', compact('status', 'statistics', 'recentLogs', 'activeServerUrl'));
    }

    /**
     * Get server status (AJAX)
     */
    public function status()
    {
        $status = $this->whatsappService->getStatus();
        return response()->json($status);
    }

    /**
     * Get server health metrics (AJAX)
     */
    public function health()
    {
        $health = $this->whatsappService->getHealth();
        return response()->json($health);
    }

    /**
     * Get QR code (AJAX)
     */
    public function qrCode()
    {
        $qr = $this->whatsappService->getQRCode();
        return response()->json($qr);
    }

    /**
     * Diagnostics page data (AJAX)
     */
    public function diagnostics()
    {
        $status = $this->whatsappService->getStatus();
        $health = $this->whatsappService->getHealth();
        
        $issues = [];

        // Check gateway connection
        if (!($status['success'] ?? false)) {
            $issues[] = [
                'type' => 'critical',
                'title' => 'Gateway Tidak Terhubung',
                'message' => 'WhatsApp Gateway server tidak dapat dijangkau.',
                'fix' => 'restart',
            ];
        } elseif (($status['data']['status'] ?? '') !== 'connected') {
            $issues[] = [
                'type' => 'warning',
                'title' => 'WhatsApp Belum Login',
                'message' => 'Gateway online tapi belum terhubung ke WhatsApp. Scan QR Code.',
                'fix' => 'qr',
            ];
        }

        // Check failed messages today
        $failedToday = WhatsAppLog::failed()->today()->count();
        if ($failedToday > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => "{$failedToday} Pesan Gagal Hari Ini",
                'message' => 'Ada pesan yang gagal terkirim hari ini.',
                'fix' => 'retry',
            ];
        }

        // Check pending messages
        $pending = WhatsAppLog::pending()->count();
        if ($pending > 5) {
            $issues[] = [
                'type' => 'info',
                'title' => "{$pending} Pesan Pending",
                'message' => 'Ada pesan yang masih menunggu dikirim.',
                'fix' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'health' => $health,
            'issues' => $issues,
            'issues_count' => count($issues),
        ]);
    }

    /**
     * Auto-fix issues
     */
    public function autoFix(Request $request)
    {
        $fixType = $request->input('fix', 'restart');
        $result = ['success' => false, 'message' => 'Unknown fix type'];

        switch ($fixType) {
            case 'restart':
                $result = $this->whatsappService->restart();
                break;
            case 'retry':
                // Retry failed messages from today
                $failedLogs = WhatsAppLog::failed()->today()->limit(10)->get();
                $retried = 0;
                foreach ($failedLogs as $log) {
                    $sendResult = $this->whatsappService->send($log->phone, $log->message, [
                        'type' => $log->type,
                        'student_id' => $log->student_id,
                    ]);
                    if ($sendResult['success']) $retried++;
                }
                $result = [
                    'success' => true,
                    'message' => "Retried {$retried} of {$failedLogs->count()} failed messages",
                ];
                break;
        }

        return response()->json($result);
    }

    /**
     * Send message page
     */
    public function sendPage()
    {
        $templates = WhatsAppTemplate::active()->get();
        $classes = AttendanceClass::orderBy('nama_kelas')->get();
        return view('whatsapp.send', compact('templates', 'classes'));
    }

    /**
     * Send single message
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:4096',
        ]);

        $result = $this->whatsappService->send(
            $validated['phone'],
            $validated['message'],
            ['type' => 'manual', 'sent_by' => auth()->id()]
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', 'Pesan berhasil dikirim!');
        }

        return back()->withErrors(['send' => 'Gagal mengirim: ' . ($result['message'] ?? 'Unknown error')]);
    }

    /**
     * Send with template
     */
    public function sendWithTemplate(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'template_id' => 'required|exists:whatsapp_templates,id',
            'variables' => 'nullable|array',
        ]);

        $template = WhatsAppTemplate::findOrFail($validated['template_id']);
        $data = $validated['variables'] ?? [];
        
        // Add default school name
        $data['sekolah'] = $data['sekolah'] ?? AttendanceSetting::get('school_name', 'Sekolah');

        $result = $this->whatsappService->sendWithTemplate(
            $validated['phone'],
            $template->name,
            $data,
            ['type' => 'manual', 'sent_by' => auth()->id()]
        );

        return response()->json($result);
    }

    /**
     * Message logs
     */
    public function logs(Request $request)
    {
        $query = WhatsAppLog::with(['student', 'template', 'sender'])->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('whatsapp.logs', compact('logs'));
    }

    /**
     * Templates list
     */
    public function templates()
    {
        $templates = WhatsAppTemplate::latest()->get();
        return view('whatsapp.templates', compact('templates'));
    }

    /**
     * Create template form
     */
    public function createTemplate()
    {
        return view('whatsapp.template-form', ['template' => null]);
    }

    /**
     * Store template
     */
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:whatsapp_templates,name',
            'label' => 'required|string',
            'message' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:check_in,check_out,absent,reminder,custom',
            'is_active' => 'boolean',
            'auto_send' => 'boolean',
        ]);

        // Extract variables from message
        preg_match_all('/\{([a-z_]+)\}/', $validated['message'], $matches);
        $validated['variables'] = array_unique($matches[1]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_send'] = $request->boolean('auto_send', false);

        WhatsAppTemplate::create($validated);

        return redirect()->route('whatsapp.templates')->with('success', 'Template berhasil dibuat!');
    }

    /**
     * Edit template form
     */
    public function editTemplate($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        return view('whatsapp.template-form', compact('template'));
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:whatsapp_templates,name,' . $id,
            'label' => 'required|string',
            'message' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:check_in,check_out,absent,reminder,custom',
            'is_active' => 'boolean',
            'auto_send' => 'boolean',
        ]);

        preg_match_all('/\{([a-z_]+)\}/', $validated['message'], $matches);
        $validated['variables'] = array_unique($matches[1]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_send'] = $request->boolean('auto_send', false);

        $template->update($validated);

        return redirect()->route('whatsapp.templates')->with('success', 'Template berhasil diupdate!');
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('whatsapp.templates')->with('success', 'Template berhasil dihapus!');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $settings = WhatsAppSetting::orderBy('group')->orderBy('id')->get();
        $groups = $settings->groupBy('group');
        return view('whatsapp.settings', compact('settings', 'groups'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $settingsData = $request->input('settings', []);

        foreach ($settingsData as $key => $value) {
            WhatsAppSetting::set($key, $value);
        }

        WhatsAppSetting::clearCache();

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    /**
     * Broadcast page
     */
    public function broadcastPage()
    {
        $classes = AttendanceClass::orderBy('nama_kelas')->get();
        $templates = WhatsAppTemplate::active()->get();
        
        return view('whatsapp.broadcast', compact('classes', 'templates'));
    }

    /**
     * Send broadcast
     */
    public function sendBroadcast(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:attendance_classes,id',
            'message' => 'required|string|max:4096',
        ]);

        // Get students with parent phone numbers
        $query = AttendanceStudent::whereNotNull('no_hp_ortu')
            ->where('no_hp_ortu', '!=', '');

        if ($request->filled('class_id')) {
            $query->where('class_id', $validated['class_id']);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return back()->withErrors(['broadcast' => 'Tidak ada siswa dengan nomor HP orang tua.']);
        }

        // Build messages array
        $messages = $students->map(function ($student) use ($validated) {
            return [
                'phone' => $student->no_hp_ortu,
                'message' => $validated['message'],
                'student_id' => $student->id,
            ];
        })->toArray();

        // Send bulk
        $result = $this->whatsappService->sendBulk($messages, [
            'type' => 'broadcast',
            'sent_by' => auth()->id(),
        ]);

        return back()->with('success', "Broadcast selesai: {$result['success_count']} berhasil, {$result['failed_count']} gagal dari {$result['total']} total.");
    }

    /**
     * Logout from WhatsApp
     */
    public function logout()
    {
        $result = $this->whatsappService->logout();
        return response()->json($result);
    }

    /**
     * Restart WhatsApp server
     */
    public function restart()
    {
        $result = $this->whatsappService->restart();
        return response()->json($result);
    }

    /**
     * Reset (logout + restart)
     */
    public function reset()
    {
        // Step 1: Logout
        $this->whatsappService->logout();
        sleep(2);
        
        // Step 2: Restart
        $result = $this->whatsappService->restart();
        
        return response()->json([
            'success' => true,
            'message' => 'Gateway sedang di-reset. Tunggu 10-15 detik lalu scan QR baru.',
        ]);
    }
}
