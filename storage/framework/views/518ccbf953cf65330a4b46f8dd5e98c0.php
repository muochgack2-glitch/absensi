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
     <?php $__env->slot('title', null, []); ?> Input Absensi Manual <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Input Absensi Manual <?php $__env->endSlot(); ?>

    <div class="space-y-6">

        
        <?php if(session('success')): ?>
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Input Absensi Manual</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Tandai hadir / izin / sakit / alpha langsung dari admin — tanpa QR scan</p>
            </div>
            <a href="<?php echo e(route('attendance.dashboard')); ?>"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Dashboard
            </a>
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
            <form method="GET" action="<?php echo e(route('attendance.manual.index')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4" id="filterForm">
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','name' => 'date','label' => 'Tanggal','value' => $date,'max' => ''.e(now()->format('Y-m-d')).'','onchange' => 'this.form.submit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','label' => 'Tanggal','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($date),'max' => ''.e(now()->format('Y-m-d')).'','onchange' => 'this.form.submit()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'class_id','label' => 'Kelas','onchange' => 'this.form.submit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'class_id','label' => 'Kelas','onchange' => 'this.form.submit()']); ?>
                    <option value="">-- Pilih Kelas --</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>" <?php echo e($classId == $class->id ? 'selected' : ''); ?>>
                            <?php echo e($class->nama_kelas); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md transition-all">
                        <i class="fas fa-filter mr-2"></i> Tampilkan
                    </button>
                </div>
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

        
        <?php if($classId && $students->isNotEmpty()): ?>
            <form method="POST" action="<?php echo e(route('attendance.manual.store')); ?>" id="manualForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date" value="<?php echo e($date); ?>">
                <input type="hidden" name="class_id" value="<?php echo e($classId); ?>">

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
                    
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">
                                    <?php echo e($classes->find($classId)?->nama_kelas ?? ''); ?> —
                                    <?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('l, d F Y')); ?>

                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($students->count()); ?> siswa aktif</p>
                            </div>
                        </div>

                        
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">Isi semua:</span>
                            <?php $__currentLoopData = ['hadir'=>['green','Hadir'], 'izin'=>['blue','Izin'], 'sakit'=>['purple','Sakit'], 'alpha'=>['red','Alpha']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => [$color, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button"
                                        onclick="fillAll('<?php echo e($val); ?>')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all
                                            <?php echo e($color === 'green'  ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100 dark:border-green-700 dark:bg-green-900/20 dark:text-green-400' : ''); ?>

                                            <?php echo e($color === 'blue'   ? 'border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : ''); ?>

                                            <?php echo e($color === 'purple' ? 'border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100 dark:border-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : ''); ?>

                                            <?php echo e($color === 'red'    ? 'border-red-300 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400' : ''); ?>">
                                    <?php echo e($label); ?> Semua
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-8">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-28">NIS</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-44">Status Kehadiran</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-28">Jam Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $existing = $records->get($student->id);
                                        $hasRecord = !is_null($existing);
                                    ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors <?php echo e($hasRecord ? 'bg-blue-50/30 dark:bg-blue-900/10' : ''); ?>"
                                        id="row_<?php echo e($student->id); ?>">
                                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs"><?php echo e($i + 1); ?></td>

                                        
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    <?php echo e(strtoupper(substr($student->nama, 0, 1))); ?>

                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white"><?php echo e($student->nama); ?></span>
                                            </div>
                                        </td>

                                        
                                        <td class="px-4 py-3 font-mono text-gray-500 dark:text-gray-400 text-xs"><?php echo e($student->nis); ?></td>

                                        
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="entries[<?php echo e($i); ?>][student_id]" value="<?php echo e($student->id); ?>">
                                            <div class="flex gap-1 justify-center flex-wrap">
                                                <?php
                                                    $currentStatus = $existing?->status ?? 'skip';
                                                    $statusOpts = [
                                                        'hadir'     => ['bg-green-500', 'H'],
                                                        'terlambat' => ['bg-yellow-500', 'T'],
                                                        'izin'      => ['bg-blue-500', 'I'],
                                                        'sakit'     => ['bg-purple-500', 'S'],
                                                        'alpha'     => ['bg-red-500', 'A'],
                                                        'skip'      => ['bg-gray-300 dark:bg-gray-600', '—'],
                                                    ];
                                                ?>
                                                <?php $__currentLoopData = $statusOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => [$bg, $lbl]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label class="relative cursor-pointer" title="<?php echo e(ucfirst($val)); ?>">
                                                        <input type="radio"
                                                               name="entries[<?php echo e($i); ?>][status]"
                                                               value="<?php echo e($val); ?>"
                                                               class="sr-only status-radio"
                                                               data-row="<?php echo e($student->id); ?>"
                                                               <?php echo e($currentStatus === $val ? 'checked' : ''); ?>

                                                               onchange="updateRowStyle('<?php echo e($student->id); ?>', '<?php echo e($val); ?>')">
                                                        <span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-xs font-bold transition-all ring-2 ring-transparent peer-checked:ring-gray-800
                                                            <?php echo e($bg); ?> <?php echo e($currentStatus === $val ? 'ring-2 ring-offset-1 ring-gray-700 dark:ring-gray-200 scale-110' : 'opacity-40 hover:opacity-100'); ?>"
                                                              id="badge_<?php echo e($student->id); ?>_<?php echo e($val); ?>">
                                                            <?php echo e($lbl); ?>

                                                        </span>
                                                    </label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </td>

                                        
                                        <td class="px-4 py-3">
                                            <input type="time"
                                                   name="entries[<?php echo e($i); ?>][check_in_time]"
                                                   value="<?php echo e($existing?->check_in_time ? \Carbon\Carbon::parse($existing->check_in_time)->format('H:i') : ''); ?>"
                                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-400">
                                        </td>

                                        
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                   name="entries[<?php echo e($i); ?>][notes]"
                                                   value="<?php echo e($existing?->notes ?? ''); ?>"
                                                   placeholder="Keterangan opsional..."
                                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-400">
                                        </td>

                                        
                                        <td class="px-4 py-3 text-center">
                                            <?php if($hasRecord): ?>
                                                <form action="<?php echo e(route('attendance.manual.destroy', $existing->id)); ?>" method="POST" class="inline"
                                                      onsubmit="return confirm('Hapus record absensi <?php echo e($student->nama); ?>?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-2 py-1.5 text-xs rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 transition-all"
                                                            title="Hapus record">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-green-500 inline-block"></span> H = Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-yellow-500 inline-block"></span> T = Terlambat</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-blue-500 inline-block"></span> I = Izin</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-purple-500 inline-block"></span> S = Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-red-500 inline-block"></span> A = Alpha</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-gray-300 dark:bg-gray-600 inline-block"></span> — = Tidak diubah</span>
                    </div>

                    
                    <div class="flex items-center justify-between mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Status <strong>—</strong> berarti baris tersebut tidak akan diubah.
                            Record yang sudah ada akan diperbarui (Update).
                        </p>
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Absensi
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
            </form>

        <?php elseif($classId && $students->isEmpty()): ?>
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
                <div class="text-center py-12 text-gray-400 dark:text-gray-600">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p class="font-medium">Tidak ada siswa aktif di kelas ini.</p>
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
                <div class="text-center py-14">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-4xl mx-auto mb-4">
                        📝
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Tanggal & Kelas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto">
                        Pilih tanggal dan kelas di atas untuk menampilkan daftar siswa yang bisa diisi absensinya secara manual.
                    </p>
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

    <?php $__env->startPush('scripts'); ?>
    <script>
        // ===== Style update saat radio dipilih =====
        const allStatuses = ['hadir','terlambat','izin','sakit','alpha','skip'];
        const statusBg = {
            hadir:     'bg-green-500',
            terlambat: 'bg-yellow-500',
            izin:      'bg-blue-500',
            sakit:     'bg-purple-500',
            alpha:     'bg-red-500',
            skip:      'bg-gray-300 dark:bg-gray-600',
        };

        function updateRowStyle(studentId, selectedStatus) {
            allStatuses.forEach(s => {
                const badge = document.getElementById('badge_' + studentId + '_' + s);
                if (!badge) return;
                if (s === selectedStatus) {
                    badge.classList.remove('opacity-40');
                    badge.classList.add('ring-2', 'ring-offset-1', 'ring-gray-700', 'dark:ring-gray-200', 'scale-110');
                } else {
                    badge.classList.add('opacity-40');
                    badge.classList.remove('ring-2', 'ring-offset-1', 'ring-gray-700', 'dark:ring-gray-200', 'scale-110');
                }
            });
        }

        // ===== Isi semua baris dengan satu status =====
        function fillAll(status) {
            document.querySelectorAll('.status-radio[value="' + status + '"]').forEach(radio => {
                radio.checked = true;
                updateRowStyle(radio.dataset.row, status);
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/manual/index.blade.php ENDPATH**/ ?>