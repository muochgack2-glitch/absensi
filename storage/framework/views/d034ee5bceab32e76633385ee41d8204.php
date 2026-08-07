<?php use Illuminate\Support\Facades\Storage; ?>
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
     <?php $__env->slot('title', null, []); ?> Rekap Semester <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Rekap Kehadiran Semester <?php $__env->endSlot(); ?>

    <div class="space-y-6">

        
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
            <div class="flex items-center mb-5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl mr-4">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Filter Rekap Semester</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih semester, tahun ajaran, dan kelas</p>
                </div>
            </div>

            <form method="GET" action="<?php echo e(route('attendance.reports.semester')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
                    <select name="semester" id="semesterSelect" onchange="updateTahunAjaran()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        <option value="ganjil" <?php echo e($semester === 'ganjil' ? 'selected' : ''); ?>>Ganjil (Jul – Des)</option>
                        <option value="genap"  <?php echo e($semester === 'genap'  ? 'selected' : ''); ?>>Genap  (Jan – Jun)</option>
                    </select>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        <?php for($y = $currentYear + 1; $y >= $currentYear - 3; $y--): ?>
                            <?php $ta = $y . '/' . ($y + 1); ?>
                            <option value="<?php echo e($ta); ?>" <?php echo e($tahunAjaran === $ta ? 'selected' : ''); ?>><?php echo e($ta); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <select name="class_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        <option value="">Semua Kelas</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cls->id); ?>" <?php echo e($classId == $cls->id ? 'selected' : ''); ?>><?php echo e($cls->nama_kelas); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm transition-all shadow hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i>Tampilkan
                    </button>
                </div>
            </form>

            <?php if(!empty($rekap)): ?>
            
            <div class="mt-4 flex flex-wrap gap-3 text-xs">
                <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> – <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?>

                </span>
                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full font-medium">
                    <i class="fas fa-school mr-1"></i><?php echo e($totalHari); ?> hari sekolah
                </span>
                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full font-medium">
                    <i class="fas fa-users mr-1"></i><?php echo e(count($rekap)); ?> siswa
                </span>
            </div>
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

        <?php if(!empty($rekap)): ?>
        
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
                <h3 class="font-bold text-gray-900 dark:text-white text-base">
                    📊 Rekap Semester <?php echo e(ucfirst($semester)); ?> — <?php echo e($tahunAjaran); ?>

                </h3>
                <div class="flex items-center gap-2">
                    
                    <a href="<?php echo e(route('attendance.reports.semester.pdf', request()->query())); ?>"
                       target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-all shadow">
                        <i class="fas fa-file-pdf mr-1.5"></i>PDF
                    </a>
                    
                    <a href="<?php echo e(route('attendance.reports.semester.excel', request()->query())); ?>"
                       class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-all shadow">
                        <i class="fas fa-file-excel mr-1.5"></i>Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400 rounded-l-lg">No</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400">NIS</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400">Nama Siswa</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400">Kelas</th>
                            <th class="px-3 py-2 font-semibold text-center text-green-600 dark:text-green-400">Hadir</th>
                            <th class="px-3 py-2 font-semibold text-center text-yellow-600 dark:text-yellow-400">Terlambat</th>
                            <th class="px-3 py-2 font-semibold text-center text-blue-600 dark:text-blue-400">Izin</th>
                            <th class="px-3 py-2 font-semibold text-center text-purple-600 dark:text-purple-400">Sakit</th>
                            <th class="px-3 py-2 font-semibold text-center text-red-600 dark:text-red-400">Alpha</th>
                            <th class="px-3 py-2 font-semibold text-center text-gray-600 dark:text-gray-400">% Hadir</th>
                            <th class="px-3 py-2 font-semibold text-center text-gray-600 dark:text-gray-400 rounded-r-lg">Ket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $__currentLoopData = $rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $persen = $totalHari > 0 ? round(($row['hadir'] / $totalHari) * 100, 1) : 0;
                            $ket = $persen >= 75 ? 'BAIK' : ($persen >= 50 ? 'CUKUP' : 'KURANG');
                            $ketColor = $persen >= 75 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                            $bgRow = $persen < 75 ? 'bg-red-50/30 dark:bg-red-900/10' : '';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 <?php echo e($bgRow); ?>">
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400"><?php echo e($i + 1); ?></td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 font-mono"><?php echo e($row['nis']); ?></td>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white"><?php echo e($row['nama']); ?></td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400"><?php echo e($row['kelas']); ?></td>
                            <td class="px-3 py-2 text-center font-bold text-green-600 dark:text-green-400"><?php echo e($row['hadir']); ?></td>
                            <td class="px-3 py-2 text-center text-yellow-600 dark:text-yellow-400"><?php echo e($row['terlambat']); ?></td>
                            <td class="px-3 py-2 text-center text-blue-600 dark:text-blue-400"><?php echo e($row['izin']); ?></td>
                            <td class="px-3 py-2 text-center text-purple-600 dark:text-purple-400"><?php echo e($row['sakit']); ?></td>
                            <td class="px-3 py-2 text-center font-bold text-red-600 dark:text-red-400"><?php echo e($row['alpha']); ?></td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold <?php echo e($persen >= 75 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : ($persen >= 50 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400')); ?>">
                                    <?php echo e($persen); ?>%
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center font-semibold <?php echo e($ketColor); ?>"><?php echo e($ket); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-gray-700 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-gray-700 dark:text-gray-300 rounded-l-lg">TOTAL</td>
                            <td class="px-3 py-2 text-center text-green-600 dark:text-green-400"><?php echo e(collect($rekap)->sum('hadir')); ?></td>
                            <td class="px-3 py-2 text-center text-yellow-600 dark:text-yellow-400"><?php echo e(collect($rekap)->sum('terlambat')); ?></td>
                            <td class="px-3 py-2 text-center text-blue-600 dark:text-blue-400"><?php echo e(collect($rekap)->sum('izin')); ?></td>
                            <td class="px-3 py-2 text-center text-purple-600 dark:text-purple-400"><?php echo e(collect($rekap)->sum('sakit')); ?></td>
                            <td class="px-3 py-2 text-center text-red-600 dark:text-red-400"><?php echo e(collect($rekap)->sum('alpha')); ?></td>
                            <td colspan="2" class="px-3 py-2 rounded-r-lg"></td>
                        </tr>
                    </tfoot>
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
        <?php elseif(request()->hasAny(['semester', 'class_id'])): ?>
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
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                <p>Tidak ada data absensi untuk filter yang dipilih.</p>
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
        <?php else: ?>
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
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-graduation-cap text-5xl mb-4 block opacity-30"></i>
                <p class="text-base font-medium">Pilih semester, tahun ajaran, dan kelas lalu klik <strong>Tampilkan</strong></p>
                <p class="text-sm mt-1">Data rekap kehadiran se-semester akan muncul di sini</p>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/reports/semester.blade.php ENDPATH**/ ?>