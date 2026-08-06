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
     <?php $__env->slot('title', null, []); ?> Kirim Pesan <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Kirim Pesan WhatsApp <?php $__env->endSlot(); ?>

    <div class="max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📤 Kirim Pesan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kirim pesan WhatsApp manual atau menggunakan template</p>
            </div>
            <a href="<?php echo e(route('whatsapp.index')); ?>" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button onclick="showTab('manual')" id="tabManual" class="px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 dark:text-blue-400">
                <i class="fas fa-edit mr-1.5"></i>Manual
            </button>
            <button onclick="showTab('template')" id="tabTemplate" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-file-alt mr-1.5"></i>Template
            </button>
        </div>

        
        <div id="panelManual">
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
                <form action="<?php echo e(route('whatsapp.send.submit')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
                        <input type="text" name="phone" placeholder="628xxxxxxxxxx" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="<?php echo e(old('phone')); ?>">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: 628xxxxxxxxxx (tanpa + atau spasi)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan</label>
                        <textarea name="message" rows="6" required placeholder="Ketik pesan WhatsApp..."
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('message')); ?></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan *teks* untuk bold, _teks_ untuk italic</p>
                    </div>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                    </button>
                </form>
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

        
        <div id="panelTemplate" class="hidden">
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
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
                        <input type="text" id="templatePhone" placeholder="628xxxxxxxxxx"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Template</label>
                        <select id="templateSelect" onchange="loadTemplateVars()"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Template --</option>
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($template->id); ?>" data-variables="<?php echo e(json_encode($template->getAvailableVariables())); ?>" data-message="<?php echo e($template->message); ?>">
                                    <?php echo e($template->label); ?> (<?php echo e($template->type_label); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div id="templateVarsContainer" class="hidden space-y-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Variables</label>
                        <div id="templateVarFields"></div>
                    </div>
                    <div id="templatePreview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preview</label>
                        <div id="previewContent" class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line font-mono"></div>
                    </div>
                    <button onclick="sendWithTemplate()" class="inline-flex items-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim dengan Template
                    </button>
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

        
        <?php if(session('success')): ?>
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 text-red-700 dark:text-red-400">
                <i class="fas fa-times-circle mr-2"></i><?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function showTab(tab) {
            document.getElementById('panelManual').classList.toggle('hidden', tab !== 'manual');
            document.getElementById('panelTemplate').classList.toggle('hidden', tab !== 'template');
            document.getElementById('tabManual').classList.toggle('border-blue-600', tab === 'manual');
            document.getElementById('tabManual').classList.toggle('text-blue-600', tab === 'manual');
            document.getElementById('tabManual').classList.toggle('border-transparent', tab !== 'manual');
            document.getElementById('tabTemplate').classList.toggle('border-blue-600', tab === 'template');
            document.getElementById('tabTemplate').classList.toggle('text-blue-600', tab === 'template');
            document.getElementById('tabTemplate').classList.toggle('border-transparent', tab !== 'template');
        }

        function loadTemplateVars() {
            const select = document.getElementById('templateSelect');
            const option = select.options[select.selectedIndex];
            const varsContainer = document.getElementById('templateVarsContainer');
            const fields = document.getElementById('templateVarFields');
            
            if (!option.value) { varsContainer.classList.add('hidden'); return; }
            
            const variables = JSON.parse(option.dataset.variables || '[]');
            varsContainer.classList.remove('hidden');
            
            fields.innerHTML = variables.map(v => `
                <div>
                    <label class="text-xs text-gray-500 dark:text-gray-400">{${v}}</label>
                    <input type="text" data-var="${v}" placeholder="${v}" oninput="updatePreview()"
                        class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            `).join('');
            
            updatePreview();
        }

        function updatePreview() {
            const select = document.getElementById('templateSelect');
            const option = select.options[select.selectedIndex];
            if (!option.value) return;
            
            let message = option.dataset.message;
            document.querySelectorAll('#templateVarFields input').forEach(input => {
                message = message.replace(new RegExp(`\\{${input.dataset.var}\\}`, 'g'), input.value || `{${input.dataset.var}}`);
            });
            
            document.getElementById('templatePreview').classList.remove('hidden');
            document.getElementById('previewContent').textContent = message;
        }

        function sendWithTemplate() {
            const phone = document.getElementById('templatePhone').value;
            const templateId = document.getElementById('templateSelect').value;
            if (!phone || !templateId) { alert('Isi nomor HP dan pilih template'); return; }
            
            const variables = {};
            document.querySelectorAll('#templateVarFields input').forEach(input => {
                variables[input.dataset.var] = input.value;
            });
            
            fetch('<?php echo e(route("whatsapp.send.template")); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                body: JSON.stringify({ phone, template_id: templateId, variables })
            })
            .then(r => r.json())
            .then(data => {
                alert(data.success ? 'Pesan berhasil dikirim!' : 'Gagal: ' + (data.message || 'Unknown error'));
            })
            .catch(err => alert('Error: ' + err.message));
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/whatsapp/send.blade.php ENDPATH**/ ?>