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
     <?php $__env->slot('title', null, []); ?> Notifikasi <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pusat Notifikasi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ringkasan semua hal yang membutuhkan perhatian Anda</p>
        </div>

        <?php
            $pendingIzin = \App\Models\AttendanceIzin::with('student')->where('status', 'pending')->orderByDesc('created_at')->get();
            
            $todayAlpha = \App\Models\AttendanceRecord::with('student')
                ->whereDate('date', today())
                ->where('status', 'alpha')
                ->get();

            $waFailed = \App\Models\WhatsAppLog::where('status', 'failed')
                ->whereDate('created_at', today())
                ->count();
        ?>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-yellow-200 dark:border-yellow-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-envelope text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($pendingIzin->count()); ?></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Izin Menunggu</div>
                    </div>
                </div>
                <?php if($pendingIzin->count() > 0): ?>
                <a href="<?php echo e(route('attendance.izin.index')); ?>" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Kelola Izin
                </a>
                <?php endif; ?>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($todayAlpha->count()); ?></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Alpha Hari Ini</div>
                    </div>
                </div>
                <?php if($todayAlpha->count() > 0): ?>
                <a href="<?php echo e(route('attendance.dashboard')); ?>" class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Lihat Dashboard
                </a>
                <?php endif; ?>
            </div>

            
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-blue-200 dark:border-blue-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fab fa-whatsapp text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($waFailed); ?></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">WA Gagal Kirim</div>
                    </div>
                </div>
                <?php if($waFailed > 0): ?>
                <a href="<?php echo e(route('whatsapp.logs')); ?>" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Lihat Log WA
                </a>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($pendingIzin->count() > 0): ?>
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
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-envelope-open-text mr-2 text-yellow-500"></i>
                Izin Menunggu Persetujuan
            </h3>
            <div class="space-y-3">
                <?php $__currentLoopData = $pendingIzin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $izin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-sm">
                            <?php echo e(substr($izin->student->nama ?? '?', 0, 1)); ?>

                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($izin->student->nama ?? '-'); ?></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <?php echo e(ucfirst($izin->jenis)); ?> • <?php echo e($izin->tanggal_mulai->format('d M')); ?>

                                <?php if($izin->tanggal_mulai != $izin->tanggal_selesai): ?>
                                    - <?php echo e($izin->tanggal_selesai->format('d M')); ?>

                                <?php endif; ?>
                                • <?php echo e($izin->created_at->diffForHumans()); ?>

                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="<?php echo e(route('attendance.izin.approve', $izin->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors">
                                <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('attendance.izin.reject', $izin->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                                <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php endif; ?>

        
        <?php if($todayAlpha->count() > 0): ?>
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
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user-times mr-2 text-red-500"></i>
                Siswa Alpha Hari Ini (<?php echo e(now()->translatedFormat('l, d M Y')); ?>)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <?php $__currentLoopData = $todayAlpha; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white font-bold text-xs">
                        <?php echo e(substr($record->student->nama ?? '?', 0, 1)); ?>

                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm"><?php echo e($record->student->nama ?? '-'); ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($record->student->kelas->nama_kelas ?? '-'); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php endif; ?>

        
        <?php if($pendingIzin->count() == 0 && $todayAlpha->count() == 0 && $waFailed == 0): ?>
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
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Semua Aman! 🎉</h3>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi yang memerlukan perhatian Anda saat ini.</p>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/notifications/index.blade.php ENDPATH**/ ?>