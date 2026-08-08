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
     <?php $__env->slot('title', null, []); ?> Gateway Management <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Gateway Management <?php $__env->endSlot(); ?>

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
                <a href="<?php echo e(route('whatsapp.index')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
            </div>
        </div>

        
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
                        <?php echo e($failoverSettings['enabled'] ? 'checked' : ''); ?>

                        onchange="toggleFailover(this.checked)"
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" id="gateway-<?php echo e($key); ?>">
                
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700
                    <?php echo e($gateway['online'] ? 'bg-green-50 dark:bg-green-900/10' : 'bg-red-50 dark:bg-red-900/10'); ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full <?php echo e($gateway['online'] ? 'bg-green-500 animate-pulse' : 'bg-red-500'); ?>"></div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white"><?php echo e($gateway['info']['name']); ?></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($gateway['info']['purpose']); ?></p>
                            </div>
                        </div>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            <?php echo e($gateway['online'] ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'); ?>">
                            <?php echo e($gateway['online'] ? 'Online' : 'Offline'); ?>

                        </span>
                    </div>
                </div>

                
                <div class="p-5 space-y-4">
                    
                    <div class="text-xs">
                        <span class="text-gray-500 dark:text-gray-400">URL:</span>
                        <code class="ml-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300"><?php echo e($gateway['info']['url'] ?: 'Tidak dikonfigurasi'); ?></code>
                    </div>

                    <?php if($gateway['online']): ?>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status WA</p>
                                <p class="text-sm font-bold <?php echo e(($gateway['status']['status'] ?? '') === 'connected' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'); ?>">
                                    <?php echo e(ucfirst($gateway['status']['status'] ?? 'unknown')); ?>

                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Uptime</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    <?php echo e(isset($gateway['health']['uptime']) ? gmdate('H:i:s', $gateway['health']['uptime']) : '-'); ?>

                                </p>
                            </div>
                        </div>

                        <?php if(isset($gateway['health'])): ?>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Memory</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    <?php echo e(isset($gateway['health']['memory']) ? round($gateway['health']['memory']['heapUsed'] / 1024 / 1024, 1) . ' MB' : '-'); ?>

                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Messages</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    <?php echo e($gateway['health']['messagesHandled'] ?? '-'); ?>

                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-plug text-3xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($gateway['error'] ?? 'Server tidak dapat dijangkau'); ?></p>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($gateway['info']['url']): ?>
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <button onclick="getQR('<?php echo e($key); ?>')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition">
                            <i class="fas fa-qrcode mr-1.5"></i>QR Code
                        </button>
                        <button onclick="restartGateway('<?php echo e($key); ?>')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition">
                            <i class="fas fa-redo mr-1.5"></i>Restart
                        </button>
                        <button onclick="logoutGateway('<?php echo e($key); ?>')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                            <i class="fas fa-sign-out-alt mr-1.5"></i>Logout
                        </button>
                        <button onclick="resetGateway('<?php echo e($key); ?>')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <i class="fas fa-bomb mr-1.5"></i>Reset
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div id="qr-<?php echo e($key); ?>" class="hidden px-5 pb-5">
                    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-sm text-gray-500 mt-2">Memuat QR...</p>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
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

    <?php $__env->startPush('scripts'); ?>
    <script>
        const csrfToken = '<?php echo e(csrf_token()); ?>';
        
        // Auto-refresh every 30s
        setInterval(refreshAllStatuses, 30000);

        function refreshAllStatuses() {
            const icon = document.getElementById('refreshAllIcon');
            icon.classList.add('fa-spin');
            
            fetch('<?php echo e(route("gateway.statuses")); ?>')
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
            fetch('<?php echo e(route("gateway.toggle-failover")); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ enabled })
            }).then(r => r.json()).then(data => {
                // Visual feedback
            });
        }
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/whatsapp/gateway.blade.php ENDPATH**/ ?>