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
     <?php $__env->slot('title', null, []); ?> Tahun Ajaran <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Manajemen Tahun Ajaran <?php $__env->endSlot(); ?>

    <div class="space-y-6">

        
        <?php if(session('success')): ?>
        <div class="flex items-center gap-3 px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300">
            <i class="fas fa-check-circle text-xl text-green-500"></i>
            <div>
                <p class="font-semibold text-sm">Berhasil!</p>
                <p class="text-xs mt-0.5"><?php echo e(session('success')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="flex items-center gap-3 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-800 dark:text-red-300">
            <i class="fas fa-exclamation-circle text-xl text-red-500"></i>
            <div>
                <p class="font-semibold text-sm">Gagal!</p>
                <p class="text-xs mt-0.5"><?php echo e(session('error')); ?></p>
            </div>
        </div>
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl shadow-lg">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Ajaran Aktif</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($activeTahun); ?></h2>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('modalNaikKelas').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-semibold text-sm transition-all shadow">
                        <i class="fas fa-level-up-alt"></i>
                        Naik Kelas
                    </button>
                    <button onclick="document.getElementById('modalBuatBaru').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold text-sm transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus"></i>
                        Tahun Baru
                    </button>
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

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border <?php echo e($ta->isActive() ? 'border-indigo-300 dark:border-indigo-700 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'); ?> p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo e($ta->tahun); ?></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <?php if($ta->started_at): ?> Mulai: <?php echo e($ta->started_at->format('d M Y')); ?> <?php endif; ?>
                        </p>
                    </div>
                    <?php if($ta->isActive()): ?>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>AKTIF
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-bold rounded-full">
                            <i class="fas fa-archive mr-1"></i>ARSIP
                        </span>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-white dark:bg-gray-900/50 rounded-xl p-3 text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo e($ta->stats['total_siswa'] ?? 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Siswa</p>
                    </div>
                    <div class="bg-white dark:bg-gray-900/50 rounded-xl p-3 text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-xl font-bold text-green-600 dark:text-green-400"><?php echo e(number_format($ta->stats['total_record'] ?? 0)); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Record</p>
                    </div>
                </div>

                <?php if(!$ta->isActive()): ?>
                <form method="POST" action="<?php echo e(route('attendance.tahun-ajaran.activate', $ta)); ?>"
                      onsubmit="return confirm('Aktifkan tahun ajaran <?php echo e($ta->tahun); ?>?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-power-off mr-1"></i> Aktifkan
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($alumni->isNotEmpty()): ?>
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
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-graduation-cap mr-2 text-amber-500"></i>Alumni / Lulus
                </h3>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    <?php echo e($alumni->count()); ?> siswa
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">NIS</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Nama</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Kelas Terakhir</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Tahun Ajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $alumni->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-gray-50 dark:border-gray-800">
                            <td class="py-2 px-2 text-gray-600 dark:text-gray-400 text-xs font-mono"><?php echo e($alu->nis); ?></td>
                            <td class="py-2 px-2 text-gray-900 dark:text-white text-xs font-medium"><?php echo e($alu->nama); ?></td>
                            <td class="py-2 px-2 text-gray-600 dark:text-gray-400 text-xs"><?php echo e($alu->kelas?->nama_kelas ?? '-'); ?></td>
                            <td class="py-2 px-2">
                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-xs">
                                    <?php echo e($alu->tahun_ajaran ?? '-'); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php if($alumni->count() > 20): ?>
                <p class="text-xs text-gray-400 text-center mt-2">Menampilkan 20 dari <?php echo e($alumni->count()); ?> alumni</p>
                <?php endif; ?>
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

        
        <?php if($promotions->isNotEmpty()): ?>
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
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2 text-indigo-500"></i>Riwayat Naik Kelas
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Tanggal</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Dari → Ke</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Naik</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Lulus</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Oleh</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Status</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-2 text-gray-700 dark:text-gray-300">
                                <?php echo e($promo->processed_at?->format('d M Y H:i')); ?>

                            </td>
                            <td class="py-3 px-2">
                                <span class="font-medium text-gray-900 dark:text-white"><?php echo e($promo->from_tahun_ajaran); ?></span>
                                <i class="fas fa-arrow-right text-xs text-gray-400 mx-1"></i>
                                <span class="font-medium text-indigo-600 dark:text-indigo-400"><?php echo e($promo->to_tahun_ajaran); ?></span>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg font-bold text-xs">
                                    <?php echo e($promo->total_promoted); ?>

                                </span>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg font-bold text-xs">
                                    <?php echo e($promo->total_graduated); ?>

                                </span>
                            </td>
                            <td class="py-3 px-2 text-gray-600 dark:text-gray-400">
                                <?php echo e($promo->processedBy?->name ?? '-'); ?>

                            </td>
                            <td class="py-3 px-2">
                                <?php if($promo->is_rolled_back): ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-xs font-medium">
                                        <i class="fas fa-undo mr-1"></i>Di-undo
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium">
                                        <i class="fas fa-check mr-1"></i>Aktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <?php if($promo->canRollback()): ?>
                                <form method="POST" action="<?php echo e(route('attendance.tahun-ajaran.rollback')); ?>"
                                      onsubmit="return confirm('UNDO naik kelas ini? Siswa akan dikembalikan ke keadaan sebelumnya. Tindakan ini tidak bisa dibatalkan!')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="promotion_id" value="<?php echo e($promo->id); ?>">
                                    <button type="submit" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-lg text-xs font-medium transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Undo
                                    </button>
                                </form>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php endif; ?>

    </div>

    
    
    
    <div id="modalBuatBaru" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalBuatBaru').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tahun Ajaran Baru</h3>
                </div>
                <button onclick="document.getElementById('modalBuatBaru').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('attendance.tahun-ajaran.create')); ?>"
                  onsubmit="return confirm('Buat tahun ajaran baru? Tahun aktif saat ini (<?php echo e($activeTahun); ?>) akan diarsipkan.')">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran Baru</label>
                    <input type="text" name="tahun" value="<?php echo e($suggestNext); ?>" placeholder="Contoh: 2027/2028"
                           pattern="\d{4}/\d{4}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-lg font-bold text-center focus:ring-2 focus:ring-indigo-400">
                    <p class="text-xs text-gray-400 mt-1.5">Format: YYYY/YYYY</p>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl mb-5">
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Perhatian:</strong> Tahun <?php echo e($activeTahun); ?> akan otomatis diarsipkan. Data lama tetap tersimpan.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modalBuatBaru').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium text-sm hover:from-indigo-700 hover:to-purple-700 transition-all shadow">
                        <i class="fas fa-plus mr-1"></i> Buat
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    
    
    <div id="modalNaikKelas" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalNaikKelas').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full z-10 flex flex-col" style="max-height: 85vh;">
            
            <div class="flex items-center justify-between p-5 pb-3 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center text-white text-sm">
                        <i class="fas fa-level-up-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Naik Kelas Massal</h3>
                    </div>
                </div>
                <button onclick="document.getElementById('modalNaikKelas').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            
            <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-50 dark:border-gray-700/50 shrink-0">
                <div id="stepIndicator1" class="flex items-center gap-1 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[9px]">1</span> Pilih
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <div id="stepIndicator2" class="flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]">2</span> Preview
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <div id="stepIndicator3" class="flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]">3</span> Proses
                </div>
            </div>

            
            <div class="overflow-y-auto flex-1 px-5 py-4">

                
                <div id="wizardStep1">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Dari Tahun</label>
                            <select id="nkTahunLama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                                <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ta->tahun); ?>" <?php echo e(!$ta->isActive() ? '' : 'selected'); ?>><?php echo e($ta->tahun); ?> <?php echo e($ta->isActive() ? '(aktif)' : ''); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ke Tahun</label>
                            <select id="nkTahunBaru" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                                <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ta->tahun); ?>" <?php echo e($ta->isActive() ? 'selected' : ''); ?>><?php echo e($ta->tahun); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        <i class="fas fa-exchange-alt mr-1"></i> Mapping Kelas
                    </label>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto mb-3 border border-gray-100 dark:border-gray-700 rounded-lg p-2">
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $nextTingkat = match($kelas->tingkat) {
                                '10' => '11', '11' => '12', '12' => null,
                                'X' => 'XI', 'XI' => 'XII', 'XII' => null,
                                default => null,
                            };
                            $nextKelas = $nextTingkat ? $classes->first(fn($k) => $k->tingkat === $nextTingkat && $k->jurusan === $kelas->jurusan) : null;
                        ?>
                        <div class="flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 dark:bg-gray-900/50 rounded text-xs">
                            <span class="font-medium text-gray-700 dark:text-gray-300 w-28 truncate"><?php echo e($kelas->nama_kelas); ?></span>
                            <i class="fas fa-arrow-right text-[10px] text-gray-400"></i>
                            <?php if($nextKelas): ?>
                                <span class="font-medium text-green-600 dark:text-green-400"><?php echo e($nextKelas->nama_kelas); ?></span>
                                <input type="hidden" class="nk-mapping" data-from="<?php echo e($kelas->id); ?>" data-to="<?php echo e($nextKelas->id); ?>">
                            <?php else: ?>
                                <span class="font-medium text-red-500 dark:text-red-400"><i class="fas fa-graduation-cap mr-1"></i>Lulus</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div id="wizardStep2" class="hidden">
                    <div id="previewContent"></div>

                    <div class="mt-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan (opsional)</label>
                        <textarea id="nkNotes" rows="2" placeholder="Contoh: Kenaikan kelas TP 2027/2028"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-xs"></textarea>
                    </div>
                </div>
            </div>

            
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 shrink-0 bg-white dark:bg-gray-800 rounded-b-2xl">
                
                <div id="footerStep1">
                    <button onclick="loadPreview()" id="btnPreview"
                            class="w-full px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium text-sm transition-all shadow hover:from-indigo-700 hover:to-purple-700">
                        <i class="fas fa-eye mr-1"></i> Lihat Preview
                    </button>
                </div>
                
                <div id="footerStep2" class="hidden flex gap-3">
                    <button onclick="goToStep(1)" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button onclick="submitNaikKelas()" id="btnSubmitNK"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-medium text-sm hover:from-amber-600 hover:to-orange-600 transition-all shadow">
                        <i class="fas fa-check mr-1"></i> Proses Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <form id="formNaikKelas" method="POST" action="<?php echo e(route('attendance.tahun-ajaran.naik-kelas')); ?>" class="hidden">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="tahun_lama" id="frmTahunLama">
        <input type="hidden" name="tahun_baru" id="frmTahunBaru">
        <input type="hidden" name="notes" id="frmNotes">
        <div id="frmMappingContainer"></div>
    </form>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // ============================================
        // WIZARD NAIK KELAS — E-Kaldik Style
        // ============================================

        function goToStep(step) {
            document.getElementById('wizardStep1').classList.toggle('hidden', step !== 1);
            document.getElementById('wizardStep2').classList.toggle('hidden', step !== 2);
            document.getElementById('footerStep1').classList.toggle('hidden', step !== 1);
            document.getElementById('footerStep2').classList.toggle('hidden', step !== 2);
            if (step === 2) document.getElementById('footerStep2').classList.add('flex');

            // Update step indicators
            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById('stepIndicator' + i);
                const dot = el.querySelector('span');
                if (i <= step) {
                    el.className = 'flex items-center gap-1 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold';
                    dot.className = 'w-4 h-4 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[9px]';
                } else {
                    el.className = 'flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold';
                    dot.className = 'w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]';
                }
            }
        }

        async function loadPreview() {
            const btn = document.getElementById('btnPreview');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat preview...';

            const tahunLama = document.getElementById('nkTahunLama').value;
            const tahunBaru = document.getElementById('nkTahunBaru').value;

            // Collect mapping
            const mapping = {};
            document.querySelectorAll('.nk-mapping').forEach(el => {
                mapping[el.dataset.from] = el.dataset.to;
            });

            try {
                const res = await fetch('<?php echo e(route("attendance.tahun-ajaran.preview")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tahun_lama: tahunLama, tahun_baru: tahunBaru, mapping })
                });

                const data = await res.json();

                if (data.success) {
                    renderPreview(data.preview);
                    goToStep(2);
                } else {
                    alert('Gagal memuat preview');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-eye mr-1"></i> Lihat Preview';
        }

        function renderPreview(preview) {
            let html = `
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                            Preview: ${preview.from_year} → ${preview.to_year}
                        </h4>
                        <span class="text-xs text-gray-500">${preview.total_students} siswa total</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${preview.total_promoted}</p>
                            <p class="text-xs text-green-700 dark:text-green-300">Naik Kelas</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">${preview.total_graduated}</p>
                            <p class="text-xs text-amber-700 dark:text-amber-300">Lulus / Alumni</p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
            `;

            preview.items.forEach(item => {
                const isGraduate = item.action === 'graduate';
                const color = isGraduate ? 'amber' : 'green';
                html += `
                    <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-lg text-sm border border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-700 dark:text-gray-300 w-28 truncate">${item.source_class}</span>
                        <i class="fas fa-arrow-right text-xs text-gray-400"></i>
                        <span class="font-medium text-${color}-600 dark:text-${color}-400">
                            ${isGraduate ? '<i class="fas fa-graduation-cap mr-1"></i>' : ''}${item.target_class || item.target}
                        </span>
                        <span class="ml-auto text-xs text-gray-500 font-bold">${item.student_count} siswa</span>
                    </div>
                `;
            });

            html += '</div></div>';
            document.getElementById('previewContent').innerHTML = html;
        }

        function submitNaikKelas() {
            if (!confirm('Proses naik kelas massal? Siswa kelas XII akan ditandai lulus. Proses ini bisa di-UNDO nanti.')) return;

            const tahunLama = document.getElementById('nkTahunLama').value;
            const tahunBaru = document.getElementById('nkTahunBaru').value;
            const notes = document.getElementById('nkNotes').value;

            document.getElementById('frmTahunLama').value = tahunLama;
            document.getElementById('frmTahunBaru').value = tahunBaru;
            document.getElementById('frmNotes').value = notes;

            // Add mapping inputs
            const container = document.getElementById('frmMappingContainer');
            container.innerHTML = '';
            document.querySelectorAll('.nk-mapping').forEach(el => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `mapping[${el.dataset.from}]`;
                input.value = el.dataset.to;
                container.appendChild(input);
            });

            document.getElementById('formNaikKelas').submit();
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/tahun-ajaran/index.blade.php ENDPATH**/ ?>