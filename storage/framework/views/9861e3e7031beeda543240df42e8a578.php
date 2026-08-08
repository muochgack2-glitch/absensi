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
     <?php $__env->slot('title', null, []); ?> Manajemen Izin Online <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Izin Online Siswa <?php $__env->endSlot(); ?>

    <div class="space-y-5">

        
        <?php if(session('success')): ?>
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
            <i class="fas fa-check-circle text-lg"></i><span><?php echo e(session('success')); ?></span>
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
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Pengajuan Izin Masuk</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-yellow-600"><?php echo e($countPending); ?></span> pengajuan menunggu persetujuan
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    
                    <form method="GET" action="<?php echo e(route('attendance.izin.index')); ?>" class="flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300">
                            <option value="pending"   <?php echo e($status === 'pending'   ? 'selected' : ''); ?>>⏳ Pending</option>
                            <option value="disetujui" <?php echo e($status === 'disetujui' ? 'selected' : ''); ?>>✅ Disetujui</option>
                            <option value="ditolak"   <?php echo e($status === 'ditolak'   ? 'selected' : ''); ?>>❌ Ditolak</option>
                            <option value="all"       <?php echo e($status === 'all'       ? 'selected' : ''); ?>>📋 Semua</option>
                        </select>
                        <input type="hidden" name="class_id" value="<?php echo e($classId); ?>">
                    </form>

                    <a href="<?php echo e(route('izin.form')); ?>" target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">
                        <i class="fas fa-external-link-alt mr-1.5"></i>Form Publik
                    </a>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <?php if($izinList->isEmpty()): ?>
            <div class="text-center py-14 text-gray-400 dark:text-gray-500">
                <i class="fas fa-file-medical text-4xl mb-3 block opacity-30"></i>
                <p>Tidak ada pengajuan dengan status <strong><?php echo e($status); ?></strong></p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-l-lg">Siswa</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Jenis</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hidden md:table-cell">Alasan</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hidden sm:table-cell">Pelapor</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $__currentLoopData = $izinList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $izin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo e($izin->student->nama); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($izin->student->nis); ?> • <?php echo e($izin->student->kelas->nama_kelas ?? '-'); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($izin->jenis === 'sakit'): ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold rounded-full">
                                        <i class="fas fa-briefcase-medical mr-1"></i>Sakit
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-full">
                                        <i class="fas fa-calendar-times mr-1"></i>Izin
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                <div><?php echo e($izin->tanggal_mulai->format('d M Y')); ?></div>
                                <?php if($izin->tanggal_mulai != $izin->tanggal_selesai): ?>
                                    <div class="text-gray-400">s/d <?php echo e($izin->tanggal_selesai->format('d M Y')); ?></div>
                                <?php endif; ?>
                                <div class="text-indigo-500 font-medium"><?php echo e($izin->durasi); ?> hari</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 max-w-xs hidden md:table-cell">
                                <p class="line-clamp-2"><?php echo e($izin->alasan); ?></p>
                                <?php if($izin->lampiran): ?>
                                    <a href="<?php echo e(Storage::url($izin->lampiran)); ?>" target="_blank"
                                       class="text-indigo-500 hover:text-indigo-700 mt-1 inline-flex items-center gap-1">
                                        <i class="fas fa-paperclip text-xs"></i>Lampiran
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden sm:table-cell">
                                <p class="font-medium"><?php echo e($izin->nama_pelapor); ?></p>
                                <p><?php echo e($izin->no_hp_pelapor); ?></p>
                                <p class="text-gray-400"><?php echo e($izin->created_at->format('d M H:i')); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php $c = $izin->status_color; ?>
                                <span class="px-2 py-1 text-xs font-bold rounded-full
                                    bg-<?php echo e($c); ?>-100 dark:bg-<?php echo e($c); ?>-900/30 text-<?php echo e($c); ?>-700 dark:text-<?php echo e($c); ?>-400">
                                    <?php echo e($izin->status_label); ?>

                                </span>
                                <?php if($izin->catatan_admin): ?>
                                    <p class="text-xs text-gray-400 mt-1 italic"><?php echo e($izin->catatan_admin); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($izin->status === 'pending'): ?>
                                <div class="flex flex-col gap-1.5 min-w-max">
                                    
                                    <form action="<?php echo e(route('attendance.izin.approve', $izin)); ?>" method="POST"
                                          onsubmit="return confirm('Setujui izin <?php echo e($izin->student->nama); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-all">
                                            <i class="fas fa-check mr-1"></i>Setujui
                                        </button>
                                    </form>

                                    
                                    <button type="button"
                                            onclick="openRejectModal(<?php echo e($izin->id); ?>, '<?php echo e(addslashes($izin->student->nama)); ?>')"
                                            class="w-full px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-semibold rounded-lg transition-all">
                                        <i class="fas fa-times mr-1"></i>Tolak
                                    </button>
                                </div>
                                <?php else: ?>
                                <span class="text-xs text-gray-400">
                                    <?php echo e($izin->approvedBy?->name ?? '-'); ?><br>
                                    <?php echo e($izin->approved_at?->format('d M H:i')); ?>

                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4"><?php echo e($izinList->withQueryString()->links()); ?></div>
            <?php endif; ?>
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

    
    <div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Tolak Pengajuan Izin</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="rejectStudentName">-</p>

            <form id="rejectForm" method="POST">
                <?php echo csrf_field(); ?>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan_admin" rows="3" required placeholder="Tulis alasan penolakan..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-400 outline-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-all">
                        <i class="fas fa-times mr-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function openRejectModal(izinId, studentName) {
            document.getElementById('rejectStudentName').textContent = 'Siswa: ' + studentName;
            document.getElementById('rejectForm').action = '/attendance/izin/' + izinId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/izin/admin-index.blade.php ENDPATH**/ ?>