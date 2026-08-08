<x-app-layout>
    <x-slot name="title">Gateway Management</x-slot>
    <x-slot name="pageTitle">Gateway Management</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🖥️ Gateway Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Monitor dan kontrol WhatsApp Gateway servers</p>
            </div>
            <div class="flex gap-2">
                <button onclick="refreshAllStatuses()" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-sync-alt mr-2" id="refreshAllIcon"></i>Refresh
                </button>
                <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
            </div>
        </div>

        {{-- Failover Toggle --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Auto Failover</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Otomatis pindah ke backup jika primary down</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="failoverToggle" 
                        {{ $failoverSettings['enabled'] ? 'checked' : '' }}
                        onchange="toggleFailover(this.checked)"
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        {{-- Gateway Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($statuses as $key => $gateway)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" id="gateway-{{ $key }}">
                {{-- Header --}}
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700
                    {{ $gateway['online'] ? 'bg-green-50 dark:bg-green-900/10' : 'bg-red-50 dark:bg-red-900/10' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $gateway['online'] ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $gateway['info']['name'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $gateway['info']['purpose'] }}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $gateway['online'] ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $gateway['online'] ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-4">
                    {{-- Server URL --}}
                    <div class="text-xs">
                        <span class="text-gray-500 dark:text-gray-400">URL:</span>
                        <code class="ml-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">{{ $gateway['info']['url'] ?: 'Tidak dikonfigurasi' }}</code>
                    </div>

                    @if($gateway['online'])
                        {{-- Connection Status --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status WA</p>
                                <p class="text-sm font-bold {{ ($gateway['status']['status'] ?? '') === 'connected' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ ucfirst($gateway['status']['status'] ?? 'unknown') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Uptime</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ isset($gateway['health']['uptime']) ? gmdate('H:i:s', $gateway['health']['uptime']) : '-' }}
                                </p>
                            </div>
                        </div>

                        @if(isset($gateway['health']))
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Memory</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ isset($gateway['health']['memory']) ? round($gateway['health']['memory']['heapUsed'] / 1024 / 1024, 1) . ' MB' : '-' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Messages</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $gateway['health']['messagesHandled'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-plug text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $gateway['error'] ?? 'Server tidak dapat dijangkau' }}</p>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    @if($gateway['info']['url'])
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <button onclick="getQR('{{ $key }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
                            <i class="fas fa-qrcode mr-1.5"></i>QR Code
                        </button>
                        <button onclick="restartGateway('{{ $key }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition">
                            <i class="fas fa-redo mr-1.5"></i>Restart
                        </button>
                        <button onclick="logoutGateway('{{ $key }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                            <i class="fas fa-sign-out-alt mr-1.5"></i>Logout
                        </button>
                        <button onclick="resetGateway('{{ $key }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <i class="fas fa-bomb mr-1.5"></i>Reset
                        </button>
                    </div>
                    @endif
                </div>

                {{-- QR Display Area --}}
                <div id="qr-{{ $key }}" class="hidden px-5 pb-5">
                    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-sm text-gray-500 mt-2">Memuat QR...</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ============================================ --}}
        {{-- PANDUAN MENGAKTIFKAN GATEWAY --}}
        {{-- ============================================ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button onclick="document.getElementById('guideContent').classList.toggle('hidden'); this.querySelector('.fa-chevron-down')?.classList.toggle('rotate-180')" 
                    class="w-full px-5 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <i class="fas fa-terminal text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-gray-900 dark:text-white">📖 Panduan Mengaktifkan Gateway</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Langkah-langkah menjalankan gateway via console/terminal</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="guideContent" class="hidden border-t border-gray-200 dark:border-gray-700">
                <div class="p-5 space-y-6">

                    {{-- Gateway Utama (Absensi - Port 3001) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            <h4 class="font-bold text-gray-900 dark:text-white">Gateway Utama — Absensi (Port 3001)</h4>
                        </div>

                        <div class="space-y-2 ml-9">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                                Gateway utama untuk sistem absensi, berjalan di <strong>port 3001</strong>
                            </p>

                            {{-- Development --}}
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">⚡ Development (Manual)</p>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-green-400 flex-1 font-mono">cd whatsapp-server</code>
                                        <button onclick="copyCmd(this, 'cd whatsapp-server')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-green-400 flex-1 font-mono">PORT=3001 node server.js</code>
                                        <button onclick="copyCmd(this, 'PORT=3001 node server.js')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Windows --}}
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">🪟 Windows (CMD/PowerShell)</p>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-yellow-400 flex-1 font-mono">set PORT=3001 && node server.js</code>
                                        <button onclick="copyCmd(this, 'set PORT=3001 && node server.js')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- PM2 Production --}}
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">🚀 Production (PM2)</p>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 start server.js --name wa-absensi -- --port 3001</code>
                                        <button onclick="copyCmd(this, 'pm2 start server.js --name wa-absensi -- --port 3001')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-gray-500 flex-1 font-mono"># atau jika sudah ada:</code>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 restart wa-absensi</code>
                                        <button onclick="copyCmd(this, 'pm2 restart wa-absensi')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Gateway Backup (SPMB - Port 3000) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            <h4 class="font-bold text-gray-900 dark:text-white">Gateway Backup — SPMB (Port 3000)</h4>
                        </div>

                        <div class="space-y-2 ml-9">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <i class="fas fa-info-circle mr-1 text-amber-400"></i>
                                Gateway backup dari SPMB, berjalan di <strong>port 3000</strong>. Digunakan otomatis jika gateway utama down.
                            </p>

                            {{-- Development --}}
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">⚡ Development (Manual)</p>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-green-400 flex-1 font-mono">cd ../whatsapp-server</code>
                                        <button onclick="copyCmd(this, 'cd ../whatsapp-server')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-green-400 flex-1 font-mono">PORT=3000 node server.js</code>
                                        <button onclick="copyCmd(this, 'PORT=3000 node server.js')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- PM2 Production --}}
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">🚀 Production (PM2)</p>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 start server.js --name wa-spmb -- --port 3000</code>
                                        <button onclick="copyCmd(this, 'pm2 start server.js --name wa-spmb -- --port 3000')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-gray-500 flex-1 font-mono"># atau jika sudah ada:</code>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 restart wa-spmb</code>
                                        <button onclick="copyCmd(this, 'pm2 restart wa-spmb')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Monitoring Commands --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            <h4 class="font-bold text-gray-900 dark:text-white">Monitoring & Troubleshooting</h4>
                        </div>

                        <div class="space-y-2 ml-9">
                            <div class="bg-gray-900 rounded-lg p-3">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1.5 font-bold">📊 PM2 Commands</p>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 status</code>
                                        <span class="text-[10px] text-gray-500">Lihat semua proses</span>
                                        <button onclick="copyCmd(this, 'pm2 status')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 logs wa-absensi</code>
                                        <span class="text-[10px] text-gray-500">Log gateway utama</span>
                                        <button onclick="copyCmd(this, 'pm2 logs wa-absensi')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 logs wa-spmb</code>
                                        <span class="text-[10px] text-gray-500">Log gateway backup</span>
                                        <button onclick="copyCmd(this, 'pm2 logs wa-spmb')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs text-cyan-400 flex-1 font-mono">pm2 save</code>
                                        <span class="text-[10px] text-gray-500">Simpan agar auto-start</span>
                                        <button onclick="copyCmd(this, 'pm2 save')" class="text-gray-500 hover:text-white text-xs"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                                <p class="text-xs text-amber-700 dark:text-amber-400">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>Tips:</strong> Pastikan kedua gateway menggunakan <strong>nomor WA berbeda</strong>.
                                    Gateway utama (3001) dan backup (3000) masing-masing punya session & QR sendiri.
                                </p>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-xs text-blue-700 dark:text-blue-400">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    <strong>Failover:</strong> Jika Auto Failover diaktifkan, ketika gateway utama (3001) tidak merespon dalam 5 detik,
                                    sistem otomatis mengirim pesan melalui gateway backup SPMB (3000).
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- QR Modal --}}
    <div id="gatewayQRModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="qrModalTitle">📱 QR Code</h3>
                <button onclick="closeGatewayQRModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="qrModalContent" class="text-center py-4"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        
        // Auto-refresh every 30s
        setInterval(refreshAllStatuses, 30000);

        function refreshAllStatuses() {
            const icon = document.getElementById('refreshAllIcon');
            icon.classList.add('fa-spin');
            
            fetch('{{ route("gateway.statuses") }}')
                .then(r => r.json())
                .then(data => {
                    icon.classList.remove('fa-spin');
                    // Could update UI dynamically here
                })
                .catch(() => icon.classList.remove('fa-spin'));
        }

        function getQR(gateway) {
            const modal = document.getElementById('gatewayQRModal');
            const content = document.getElementById('qrModalContent');
            const title = document.getElementById('qrModalTitle');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            title.textContent = `📱 QR Code - ${gateway.charAt(0).toUpperCase() + gateway.slice(1)}`;
            content.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="text-gray-500 mt-3">Memuat QR...</p>';
            
            fetch(`/gateway/${gateway}/qr`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.qr) {
                        content.innerHTML = `
                            <img src="${data.qr}" alt="QR Code" class="mx-auto" style="width: 300px; height: 300px; image-rendering: crisp-edges;">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Scan QR ini dengan WhatsApp</p>
                        `;
                    } else {
                        content.innerHTML = `
                            <div class="text-green-500 text-4xl mb-3"><i class="fas fa-check-circle"></i></div>
                            <p class="text-gray-700 dark:text-gray-300">${data.message || 'QR tidak tersedia'}</p>
                        `;
                    }
                })
                .catch(err => {
                    content.innerHTML = `<div class="text-red-500"><i class="fas fa-times-circle text-4xl mb-3"></i><p>${err.message}</p></div>`;
                });
        }

        function closeGatewayQRModal() {
            const modal = document.getElementById('gatewayQRModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function restartGateway(gateway) {
            if (!confirm('Restart gateway ini?')) return;
            fetch(`/gateway/${gateway}/restart`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }
            }).then(r => r.json()).then(data => alert(data.message));
        }

        function logoutGateway(gateway) {
            if (!confirm('Logout dari gateway ini?')) return;
            fetch(`/gateway/${gateway}/logout`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }
            }).then(r => r.json()).then(data => alert(data.message));
        }

        function resetGateway(gateway) {
            if (!confirm('Reset gateway? Ini akan logout dan restart.')) return;
            fetch(`/gateway/${gateway}/reset`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }
            }).then(r => r.json()).then(data => alert(data.message));
        }

        function toggleFailover(enabled) {
            fetch('{{ route("gateway.toggle-failover") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ enabled })
            }).then(r => r.json()).then(data => {
                // Visual feedback
            });
        }

        function copyCmd(btn, text) {
            navigator.clipboard.writeText(text).then(() => {
                const icon = btn.querySelector('i');
                icon.className = 'fas fa-check text-green-400';
                setTimeout(() => { icon.className = 'fas fa-copy'; }, 1500);
            });
        }
    </script>
    @endpush
</x-app-layout>
