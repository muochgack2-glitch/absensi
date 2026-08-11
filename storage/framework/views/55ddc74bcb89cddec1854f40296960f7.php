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
     <?php $__env->slot('title', null, []); ?> Settings WA <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Pengaturan WhatsApp Gateway <?php $__env->endSlot(); ?>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan Gateway</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi WhatsApp Gateway server</p>
            </div>
            <a href="<?php echo e(route('whatsapp.index')); ?>" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('whatsapp.settings.update')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                        <?php echo e($group === 'general' ? 'bg-blue-100 dark:bg-blue-900/30' :
                           ($group === 'connection' ? 'bg-cyan-100 dark:bg-cyan-900/30' :
                           ($group === 'notification' ? 'bg-amber-100 dark:bg-amber-900/30' :
                           'bg-red-100 dark:bg-red-900/30'))); ?>">
                        <i class="fas <?php echo e($group === 'general' ? 'fa-cog text-blue-600 dark:text-blue-400' :
                           ($group === 'connection' ? 'fa-plug text-cyan-600 dark:text-cyan-400' :
                           ($group === 'notification' ? 'fa-bell text-amber-600 dark:text-amber-400' :
                           'fa-tools text-red-600 dark:text-red-400'))); ?>"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo e($items->first()->group_label); ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($group === 'general' ? 'Pengaturan umum gateway' : ($group === 'connection' ? 'Pengaturan koneksi & failover' : ($group === 'notification' ? 'Pengaturan rate limit & delay' : 'Pengaturan lanjutan'))); ?></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($setting->label); ?></label>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($setting->description); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <?php if($setting->type === 'boolean'): ?>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="settings[<?php echo e($setting->key); ?>]" value="false">
                                    <input type="checkbox" name="settings[<?php echo e($setting->key); ?>]" value="true"
                                        <?php echo e(filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : ''); ?>

                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            <?php elseif($setting->type === 'integer'): ?>
                                <input type="number" name="settings[<?php echo e($setting->key); ?>]" value="<?php echo e($setting->value); ?>"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <?php else: ?>
                                <input type="text" name="settings[<?php echo e($setting->key); ?>]" value="<?php echo e($setting->value); ?>"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <?php endif; ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </form>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/whatsapp/settings.blade.php ENDPATH**/ ?>