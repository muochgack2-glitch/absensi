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
     <?php $__env->slot('title', null, []); ?> Laporan <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Laporan Absensi <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Absensi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Generate dan export laporan absensi siswa</p>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="<?php echo e(route('attendance.reports.daily')); ?>" 
               class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📅</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Harian</h3>
                    <p class="text-sm text-blue-100 flex-grow">Absensi hari ini real-time</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            
            <a href="<?php echo e(route('attendance.reports.monthly')); ?>" 
               class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📆</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Bulanan</h3>
                    <p class="text-sm text-purple-100 flex-grow">Rekapitulasi per bulan</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            
            <a href="#custom" 
               class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">🔍</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Custom</h3>
                    <p class="text-sm text-orange-100 flex-grow">Filter kustom sesuai kebutuhan</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Generate</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            
            <a href="<?php echo e(route('attendance.reports.export-summary')); ?>" 
               onclick="return confirm('Export ringkasan absensi bulan ini ke Excel?')"
               class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📥</div>
                    <h3 class="text-lg font-bold mb-2">Export Excel</h3>
                    <p class="text-sm text-green-100 flex-grow">Download ringkasan bulan ini</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Download</span>
                        <i class="fas fa-download ml-2 transform group-hover:translate-y-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <a href="<?php echo e(route('attendance.reports.alpha')); ?>"
               class="group relative bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">⚠️</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Alpha</h3>
                    <p class="text-sm text-red-100 flex-grow">Siswa sering tidak hadir + kirim WA ke ortu</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat & Kirim WA</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            
            <a href="<?php echo e(route('attendance.students.export.excel')); ?>"
               class="group relative bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">👥</div>
                    <h3 class="text-lg font-bold mb-2">Export Data Siswa</h3>
                    <p class="text-sm text-teal-100 flex-grow">Download daftar semua siswa ke Excel</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Download</span>
                        <i class="fas fa-download ml-2 transform group-hover:translate-y-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['id' => 'custom']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'custom']); ?>
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-2xl mr-4">
                    <i class="fas fa-file-chart"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Generate Laporan Custom</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Filter data sesuai kebutuhan Anda</p>
                </div>
            </div>

            <form action="<?php echo e(route('attendance.reports.generate')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','name' => 'start_date','label' => 'Tanggal Mulai','value' => old('start_date', date('Y-m-01')),'required' => true,'error' => $errors->first('start_date')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'start_date','label' => 'Tanggal Mulai','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('start_date', date('Y-m-01'))),'required' => true,'error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('start_date'))]); ?>
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

                        <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','name' => 'end_date','label' => 'Tanggal Akhir','value' => old('end_date', date('Y-m-d')),'required' => true,'error' => $errors->first('end_date')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'end_date','label' => 'Tanggal Akhir','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('end_date', date('Y-m-d'))),'required' => true,'error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('end_date'))]); ?>
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
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'class_id','label' => 'Filter Kelas','error' => $errors->first('class_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'class_id','label' => 'Filter Kelas','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('class_id'))]); ?>
                            <option value="">Semua Kelas</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e(old('class_id') == $class->id ? 'selected' : ''); ?>>
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

                        <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'status','label' => 'Filter Status','error' => $errors->first('status')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','label' => 'Filter Status','error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('status'))]); ?>
                            <option value="">Semua Status</option>
                            <option value="hadir" <?php echo e(old('status') == 'hadir' ? 'selected' : ''); ?>>✅ Hadir</option>
                            <option value="terlambat" <?php echo e(old('status') == 'terlambat' ? 'selected' : ''); ?>>⏰ Terlambat</option>
                            <option value="sakit" <?php echo e(old('status') == 'sakit' ? 'selected' : ''); ?>>🤒 Sakit</option>
                            <option value="izin" <?php echo e(old('status') == 'izin' ? 'selected' : ''); ?>>📝 Izin</option>
                            <option value="alpha" <?php echo e(old('status') == 'alpha' ? 'selected' : ''); ?>>❌ Alpha</option>
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
                    </div>

                    
                    <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'format','label' => 'Format Output','required' => true,'error' => $errors->first('format')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'format','label' => 'Format Output','required' => true,'error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->first('format'))]); ?>
                        <option value="preview" <?php echo e(old('format') == 'preview' ? 'selected' : ''); ?>>👁️ Preview (di layar)</option>
                        <option value="excel" <?php echo e(old('format') == 'excel' ? 'selected' : ''); ?>>📥 Excel (.xlsx)</option>
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

                    
                    <div class="flex justify-end gap-3 pt-4">
                        <a
                            href="<?php echo e(route('attendance.dashboard')); ?>"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-search mr-2"></i>
                            Generate Laporan
                        </button>
                    </div>
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
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">💡 Panduan Penggunaan</h3>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Harian:</strong> Lihat absensi hari ini secara real-time dengan status lengkap</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Bulanan:</strong> Rekapitulasi absensi per siswa dalam 1 bulan untuk evaluasi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Custom:</strong> Filter berdasarkan tanggal, kelas, dan status tertentu sesuai kebutuhan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Export Excel:</strong> Download data dalam format .xlsx untuk analisis lebih lanjut</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Preview:</strong> Tampilkan data di layar terlebih dahulu sebelum melakukan export</span>
                        </li>
                    </ul>
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/reports/index.blade.php ENDPATH**/ ?>