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
     <?php $__env->slot('title', null, []); ?> Cetak Kartu Pelajar <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Cetak Kartu Pelajar <?php $__env->endSlot(); ?>

    <div class="max-w-4xl space-y-6" x-data="{ mode: 'class', layout: '2x5' }">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🎴 Cetak Kartu Pelajar</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Generate kartu pelajar dengan QR Code absensi</p>
            </div>
            <a href="<?php echo e(route('attendance.students.index')); ?>" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        
        <form action="<?php echo e(route('attendance.students.card.generate')); ?>" method="POST" target="_blank">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="layout" :value="layout">

            
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
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pilih Siswa</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pilih kelas atau siswa yang akan dicetak</p>
                    </div>
                </div>

                <div class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div @click="mode = 'all'" 
                             :class="mode === 'all' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                             class="p-4 rounded-xl border-2 transition-all cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-globe text-2xl text-blue-500"></i>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">Semua Siswa</p>
                                    <p class="text-xs text-gray-500"><?php echo e(\App\Models\AttendanceStudent::where('is_active', true)->count()); ?> siswa aktif</p>
                                </div>
                            </div>
                        </div>

                        <div @click="mode = 'class'" 
                             :class="mode === 'class' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                             class="p-4 rounded-xl border-2 transition-all cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-school text-2xl text-purple-500"></i>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">Per Kelas</p>
                                    <p class="text-xs text-gray-500">Pilih kelas tertentu</p>
                                </div>
                            </div>
                        </div>

                        <div @click="mode = 'selected'" 
                             :class="mode === 'selected' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                             class="p-4 rounded-xl border-2 transition-all cursor-pointer">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-square text-2xl text-green-500"></i>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">Pilih Manual</p>
                                    <p class="text-xs text-gray-500">Ketik ID siswa</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div x-show="mode === 'class'" x-transition class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Kelas</label>
                        <select name="kelas_id" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kelas --</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>">
                                    <?php echo e($class->nama_kelas); ?> (<?php echo e($class->students()->where('is_active', true)->count()); ?> siswa)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div x-show="mode === 'selected'" x-transition class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan ID Siswa (pisahkan dengan koma)</label>
                        <textarea name="student_ids" rows="3" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 1,2,3,4,5"></textarea>
                    </div>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mt-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-6']); ?>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Layout Cetak</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kertas F4 (215 × 330 mm)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div @click="layout = '2x5'" 
                         :class="layout === '2x5' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'"
                         class="p-4 rounded-xl border-2 transition-all cursor-pointer text-center">
                        <div class="text-3xl mb-2">📄</div>
                        <p class="font-bold text-gray-900 dark:text-white">2 × 5</p>
                        <p class="text-xs text-gray-500 mt-1">10 kartu/halaman</p>
                        <p class="text-xs text-green-600 font-semibold mt-1">✨ Direkomendasikan</p>
                    </div>

                    <div @click="layout = '2x4'" 
                         :class="layout === '2x4' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'"
                         class="p-4 rounded-xl border-2 transition-all cursor-pointer text-center">
                        <div class="text-3xl mb-2">📄</div>
                        <p class="font-bold text-gray-900 dark:text-white">2 × 4</p>
                        <p class="text-xs text-gray-500 mt-1">8 kartu/halaman</p>
                        <p class="text-xs text-gray-500 mt-1">Lebih besar</p>
                    </div>

                    <div @click="layout = '2x3'" 
                         :class="layout === '2x3' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'"
                         class="p-4 rounded-xl border-2 transition-all cursor-pointer text-center">
                        <div class="text-3xl mb-2">📄</div>
                        <p class="font-bold text-gray-900 dark:text-white">2 × 3</p>
                        <p class="text-xs text-gray-500 mt-1">6 kartu/halaman</p>
                        <p class="text-xs text-gray-500 mt-1">QR Paling Besar</p>
                    </div>
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

            
            <div class="flex justify-end mt-6">
                <button type="submit" 
                    class="inline-flex items-center px-8 py-3 text-sm font-bold rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white hover:from-blue-600 hover:to-purple-700 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Generate PDF Kartu Pelajar
                </button>
            </div>
        </form>

        
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
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">💡 Tips Cetak</h4>
                <ul class="space-y-1 text-sm text-blue-800 dark:text-blue-200">
                    <li>• Pastikan printer diset ke ukuran kertas <strong>F4 (215 × 330 mm)</strong></li>
                    <li>• Gunakan scale <strong>100%</strong> (jangan "Fit to Page")</li>
                    <li>• Untuk hasil terbaik, cetak di kertas tebal/karton (≥ 200 gsm)</li>
                    <li>• Pastikan logo sekolah sudah diupload di <a href="<?php echo e(route('attendance.settings.index')); ?>" class="underline font-semibold">Settings</a></li>
                </ul>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/students/card-options.blade.php ENDPATH**/ ?>