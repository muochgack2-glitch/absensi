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
     <?php $__env->slot('title', null, []); ?> Templates <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Template Pesan WhatsApp <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Template Pesan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola template pesan notifikasi WhatsApp</p>
            </div>
            <a href="<?php echo e(route('whatsapp.templates.create')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Buat Template
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo e($template->label); ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?php echo e($template->name); ?></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            <?php echo e($template->type === 'check_in' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' :
                               ($template->type === 'check_out' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                               ($template->type === 'absent' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                               'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400'))); ?>">
                            <?php echo e($template->type_label); ?>

                        </span>
                        <?php if($template->is_active): ?>
                            <span class="w-2 h-2 rounded-full bg-green-500" title="Aktif"></span>
                        <?php else: ?>
                            <span class="w-2 h-2 rounded-full bg-gray-400" title="Nonaktif"></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line font-mono text-xs max-h-32 overflow-y-auto"><?php echo e($template->message); ?></div>
                
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-3">
                        <span><i class="fas fa-chart-bar mr-1"></i><?php echo e($template->usage_count); ?>x dipakai</span>
                        <?php if($template->auto_send): ?>
                            <span class="text-amber-600 dark:text-amber-400"><i class="fas fa-bolt mr-1"></i>Auto</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('whatsapp.templates.edit', $template->id)); ?>" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-blue-600 dark:text-blue-400 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('whatsapp.templates.delete', $template->id)); ?>" method="POST" onsubmit="return confirm('Hapus template ini?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-red-600 dark:text-red-400 transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-2 text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-3"></i>
                <p>Belum ada template. <a href="<?php echo e(route('whatsapp.templates.create')); ?>" class="text-blue-600 dark:text-blue-400 hover:underline">Buat sekarang</a></p>
            </div>
            <?php endif; ?>
        </div>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/whatsapp/templates.blade.php ENDPATH**/ ?>