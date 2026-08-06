<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> WhatsApp Gateway <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> WhatsApp Gateway <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📱 WhatsApp Gateway</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Monitor dan kelola pengiriman pesan WhatsApp</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('gateway.index')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-server mr-2"></i>Gateway
                </a>
                <a href="<?php echo e(route('whatsapp.send')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                </a>
            </div>
        </div>

        
        <div id="connectionCard" class="rounded-xl border p-6 transition-all duration-300
            <?php echo e(($status['success'] ?? false) && ($status['data']['status'] ?? '') === 'connected'
                ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'); ?>">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div id="statusIcon" class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl
                        <?php echo e(($status['success'] ?? false) && ($status['data']['status'] ?? '') === 'connected'
                            ? 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400'
                            : 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-400'); ?>">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="statusTitle">
                            <?php if(($status['success'] ?? false) && ($status['data']['status'] ?? '') === 'connected'): ?>
                                ✅ Gateway Terhubung
                            <?php elseif($status['success'] ?? false): ?>
                                ⚠️ Gateway Online — Belum Login
                            <?php else: ?>
                                ❌ Gateway Offline
                            <?php endif; ?>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" id="statusSubtitle">
                            Server: <?php echo e($activeServerUrl); ?>

                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="refreshStatus()" class="p-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition" title="Refresh">
                        <i class="fas fa-sync-alt text-gray-500 dark:text-gray-400" id="refreshIcon"></i>
                    </button>
                    <button onclick="showQRModal()" class="p-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition" title="QR Code">
                        <i class="fas fa-qrcode text-gray-500 dark:text-gray-400"></i>
                    </button>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($statistics['sent_today'] ?? 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Terkirim Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($statistics['failed_today'] ?? 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Gagal Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($statistics['total_sent'] ?? 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Terkirim</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="fas fa-file-alt text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($statistics['active_templates'] ?? 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Template Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <i class="fas fa-stethoscope text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Auto-Healing Diagnostics</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Deteksi dan perbaiki masalah otomatis</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="runDiagnostics()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition">
                        <i class="fas fa-sync-alt mr-1.5"></i>Scan
                    </button>
                    <button onclick="autoFix()" id="autoFixBtn" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition">
                        <i class="fas fa-magic mr-1.5"></i>Auto-Fix
                    </button>
                </div>
            </div>
            <div id="diagnosticsResult" class="text-sm text-gray-500 dark:text-gray-400">
                <p>Klik "Scan" untuk memulai diagnostik...</p>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900 dark:text-white">📋 Log Pesan Terbaru</h3>
                <a href="<?php echo e(route('whatsapp.logs')); ?>" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">No HP</th>
                            <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Pesan</th>
                            <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Tipe</th>
                            <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Status</th>
                            <th class="text-left py-2 px-3 text-gray-600 dark:text-gray-400 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="py-2 px-3 text-gray-900 dark:text-white font-mono text-xs"><?php echo e($log->phone); ?></td>
                            <td class="py-2 px-3 text-gray-600 dark:text-gray-400 max-w-xs truncate"><?php echo e(Str::limit($log->message, 50)); ?></td>
                            <td class="py-2 px-3">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                    <?php echo e($log->type === 'check_in' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' :
                                       ($log->type === 'check_out' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                       ($log->type === 'absent' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                                       'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400'))); ?>">
                                    <?php echo e($log->type_label); ?>

                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full
                                    <?php echo e($log->status === 'sent' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                       ($log->status === 'failed' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                                       'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400')); ?>">
                                    <i class="fas <?php echo e($log->status === 'sent' ? 'fa-check' : ($log->status === 'failed' ? 'fa-times' : 'fa-clock')); ?> text-[10px]"></i>
                                    <?php echo e($log->status_label); ?>

                                </span>
                            </td>
                            <td class="py-2 px-3 text-gray-500 dark:text-gray-400 text-xs"><?php echo e($log->created_at->diffForHumans()); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p>Belum ada log pesan</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>

    
    <div id="qrModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">📱 Scan QR Code</h3>
                <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="qrContent" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-3">Memuat QR Code...</p>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Auto-refresh status every 30 seconds
        setInterval(refreshStatus, 30000);

        function refreshStatus() {
            const icon = document.getElementById('refreshIcon');
            icon.classList.add('fa-spin');
            
            fetch('<?php echo e(route("whatsapp.status")); ?>')
                .then(r => r.json())
                .then(data => {
                    icon.classList.remove('fa-spin');
                    const card = document.getElementById('connectionCard');
                    const title = document.getElementById('statusTitle');
                    
                    if (data.success && data.data && data.data.status === 'connected') {
                        card.className = 'rounded-xl border p-6 transition-all duration-300 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800';
                        title.innerHTML = '✅ Gateway Terhubung';
                    } else if (data.success) {
                        card.className = 'rounded-xl border p-6 transition-all duration-300 bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800';
                        title.innerHTML = '⚠️ Gateway Online — Belum Login';
                    } else {
                        card.className = 'rounded-xl border p-6 transition-all duration-300 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800';
                        title.innerHTML = '❌ Gateway Offline';
                    }
                })
                .catch(() => { icon.classList.remove('fa-spin'); });
        }

        function showQRModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            document.getElementById('qrContent').innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="text-gray-500 dark:text-gray-400 mt-3">Memuat QR Code...</p>';
            
            fetch('<?php echo e(route("whatsapp.qr")); ?>')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data && data.data.qr) {
                        document.getElementById('qrContent').innerHTML = `
                            <img src="${data.data.qr}" alt="QR Code" class="mx-auto" style="width: 300px; height: 300px; image-rendering: crisp-edges;">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Scan QR ini dengan WhatsApp di HP Anda</p>
                        `;
                    } else {
                        document.getElementById('qrContent').innerHTML = `
                            <div class="text-green-500 text-4xl mb-3"><i class="fas fa-check-circle"></i></div>
                            <p class="text-gray-700 dark:text-gray-300 font-medium">${data.data?.message || 'Sudah terhubung atau QR tidak tersedia'}</p>
                        `;
                    }
                })
                .catch(err => {
                    document.getElementById('qrContent').innerHTML = `
                        <div class="text-red-500 text-4xl mb-3"><i class="fas fa-exclamation-triangle"></i></div>
                        <p class="text-red-600 dark:text-red-400">Gagal memuat QR: ${err.message}</p>
                    `;
                });
        }

        function closeQRModal() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function runDiagnostics() {
            const result = document.getElementById('diagnosticsResult');
            result.innerHTML = '<div class="flex items-center gap-2"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-indigo-600"></div> Scanning...</div>';
            
            fetch('<?php echo e(route("whatsapp.diagnostics")); ?>')
                .then(r => r.json())
                .then(data => {
                    if (data.issues && data.issues.length > 0) {
                        let html = '<div class="space-y-2">';
                        data.issues.forEach(issue => {
                            const colors = {
                                critical: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400',
                                warning: 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400',
                                info: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400',
                            };
                            const icons = { critical: 'fa-times-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
                            html += `<div class="rounded-lg border p-3 ${colors[issue.type] || colors.info}">
                                <i class="fas ${icons[issue.type] || icons.info} mr-2"></i>
                                <strong>${issue.title}</strong> — ${issue.message}
                            </div>`;
                        });
                        html += '</div>';
                        result.innerHTML = html;
                    } else {
                        result.innerHTML = '<div class="rounded-lg border p-3 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-700 dark:text-green-400"><i class="fas fa-check-circle mr-2"></i>Semua sistem berjalan normal!</div>';
                    }
                })
                .catch(err => {
                    result.innerHTML = `<div class="text-red-500"><i class="fas fa-times-circle mr-2"></i>Gagal: ${err.message}</div>`;
                });
        }

        function autoFix() {
            const btn = document.getElementById('autoFixBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i>Fixing...';
            
            fetch('<?php echo e(route("whatsapp.auto-fix")); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                body: JSON.stringify({ fix: 'restart' })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1.5"></i>Auto-Fix';
                alert(data.message || 'Auto-fix selesai');
                setTimeout(() => { refreshStatus(); runDiagnostics(); }, 5000);
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1.5"></i>Auto-Fix';
            });
        }

        // Run diagnostics on page load
        document.addEventListener('DOMContentLoaded', runDiagnostics);
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/whatsapp/index.blade.php ENDPATH**/ ?>