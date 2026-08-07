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
     <?php $__env->slot('title', null, []); ?> Dashboard Absensi <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> Dashboard <?php $__env->endSlot(); ?>

    <div class="space-y-6" id="dashboard-content">
        
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
            <form method="GET" action="<?php echo e(route('attendance.dashboard')); ?>" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','name' => 'date','label' => 'Tanggal','value' => $selectedDate,'icon' => 'fa-calendar','onchange' => 'document.getElementById(\'filterForm\').submit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','label' => 'Tanggal','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedDate),'icon' => 'fa-calendar','onchange' => 'document.getElementById(\'filterForm\').submit()']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'class','label' => 'Kelas','value' => $selectedClass ?? '','icon' => 'fa-school','onchange' => 'document.getElementById(\'filterForm\').submit()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'class','label' => 'Kelas','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedClass ?? ''),'icon' => 'fa-school','onchange' => 'document.getElementById(\'filterForm\').submit()']); ?>
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>" <?php echo e($selectedClass == $class->id ? 'selected' : ''); ?>>
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

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Total Siswa','value' => $stats['total'] ?? 0,'icon' => 'fa-users','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Total Siswa','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['total'] ?? 0),'icon' => 'fa-users','color' => 'blue']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Hadir','value' => $stats['present'] ?? 0,'icon' => 'fa-check-circle','color' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Hadir','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['present'] ?? 0),'icon' => 'fa-check-circle','color' => 'green']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Terlambat','value' => $stats['late'] ?? 0,'icon' => 'fa-clock','color' => 'yellow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Terlambat','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['late'] ?? 0),'icon' => 'fa-clock','color' => 'yellow']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['title' => 'Alpha','value' => $stats['alpha'] ?? 0,'icon' => 'fa-times-circle','color' => 'red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alpha','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stats['alpha'] ?? 0),'icon' => 'fa-times-circle','color' => 'red']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            
            <div class="lg:col-span-2">
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
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">Tren Kehadiran 7 Hari</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hadir vs Alpha (hari kerja)</p>
                            </div>
                        </div>
                        
                        <select id="chartClassFilter"
                                onchange="loadChartByClass(this.value)"
                                class="text-xs px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400">
                            <option value="">Semua Kelas</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cls->id); ?>"><?php echo e($cls->nama_kelas); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div style="height:220px; position:relative;">
                        <canvas id="chartBar"></canvas>
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

            
            <div>
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
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">Status Hari Ini</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Total: <?php echo e($totalToday); ?> record
                            </p>
                        </div>
                    </div>
                    <div style="height:200px; position:relative;">
                        <canvas id="chartDonut"></canvas>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-1 text-xs">
                        <?php
                            $statusColors = ['hadir'=>'#22c55e','terlambat'=>'#f59e0b','alpha'=>'#ef4444','izin'=>'#3b82f6','sakit'=>'#a855f7'];
                            $statusLabels = ['hadir'=>'Hadir','terlambat'=>'Terlambat','alpha'=>'Alpha','izin'=>'Izin','sakit'=>'Sakit'];
                        ?>
                        <?php $__currentLoopData = $donutData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-block w-3 h-3 rounded-full" style="background:<?php echo e($statusColors[$key]); ?>"></span>
                                <span class="text-gray-600 dark:text-gray-400"><?php echo e($statusLabels[$key]); ?>: <strong class="text-gray-800 dark:text-white"><?php echo e($val); ?></strong></span>
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
            </div>
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
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-clipboard-list mr-2 text-primary-600"></i>
                    Data Absensi
                </h3>
                <button onclick="refreshDashboard()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                NIS
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kelas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Check In
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Check Out
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Foto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $attendanceRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo e($record->student->nis); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?php echo e($record->student->nama); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e($record->student->kelas->nama_kelas ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e($record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e($record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($record->status === 'hadir'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            ✅ Hadir
                                        </span>
                                    <?php elseif($record->status === 'terlambat'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                            ⏰ Terlambat
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            <?php echo e(ucfirst($record->status)); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <?php if($record->check_in_photo): ?>
                                            <button onclick="viewPhoto('<?php echo e($record->check_in_photo_url); ?>', '<?php echo e(addslashes($record->student->nama)); ?>', 'Check In')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors"
                                                    title="Lihat foto check in">
                                                <i class="fas fa-sign-in-alt text-sm"></i>
                                            </button>
                                        <?php else: ?>
                                            <div class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded">
                                                <i class="fas fa-sign-in-alt text-sm"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if($record->check_out_photo): ?>
                                            <button onclick="viewPhoto('<?php echo e($record->check_out_photo_url); ?>', '<?php echo e(addslashes($record->student->nama)); ?>', 'Check Out')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                                                    title="Lihat foto check out">
                                                <i class="fas fa-sign-out-alt text-sm"></i>
                                            </button>
                                        <?php else: ?>
                                            <div class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded">
                                                <i class="fas fa-sign-out-alt text-sm"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p>Belum ada data absensi untuk tanggal ini</p>
                                </td>
                            </tr>
                        <?php endif; ?>
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

        
        <?php if($absentStudents->count() > 0): ?>
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
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-user-times mr-2 text-red-600"></i>
                    Siswa Belum Absen (<?php echo e($absentStudents->count()); ?>)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $absentStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="font-semibold text-gray-900 dark:text-white"><?php echo e($student->nama); ?></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($student->nis); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-500"><?php echo e($student->kelas->nama_kelas ?? '-'); ?></p>
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
    </div>

    
    <div id="photoModal" class="hidden fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closePhotoModal()">
        <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
            
            <button onclick="closePhotoModal()" 
                    class="absolute -top-8 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl overflow-hidden">
                
                <div class="bg-gradient-to-r from-primary-500 to-purple-600 px-4 py-2 text-white">
                    <h3 class="text-base font-bold" id="photoModalTitle">Foto Absensi</h3>
                    <p class="text-xs opacity-90" id="photoModalSubtitle"></p>
                </div>
                
                
                <div class="p-3 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                    <img id="photoModalImage" src="" alt="Foto" class="max-w-full max-h-[40vh] rounded-lg shadow-lg object-contain">
                </div>
                
                
                <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 flex justify-between items-center">
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik di luar untuk menutup
                    </div>
                    <button onclick="downloadPhoto()" 
                            class="px-3 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        /**
         * Manual refresh dashboard data
         */
        function refreshDashboard() {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            fetch('<?php echo e(route("attendance.dashboard.refresh")); ?>?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Refresh error:', error));
        }

        // ============================================================================
        // PHOTO MODAL FUNCTIONS
        // ============================================================================

        function viewPhoto(photoUrl, studentName, type) {
            console.log('viewPhoto called:', { photoUrl, studentName, type });
            
            const modal = document.getElementById('photoModal');
            const image = document.getElementById('photoModalImage');
            const title = document.getElementById('photoModalTitle');
            const subtitle = document.getElementById('photoModalSubtitle');
            
            // Set image source and alt
            image.src = photoUrl;
            image.alt = `Foto ${type} - ${studentName}`;
            image.onerror = function() {
                console.error('Failed to load image:', photoUrl);
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle" font-family="sans-serif" font-size="18"%3EGagal memuat foto%3C/text%3E%3C/svg%3E';
            };
            
            title.textContent = `Foto ${type}`;
            subtitle.textContent = studentName;
            
            modal.classList.remove('hidden');
            
            // Add fade-in animation
            modal.style.animation = 'fadeIn 0.2s ease-out';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.style.animation = 'fadeOut 0.2s ease-out';
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
        
        function downloadPhoto() {
            const image = document.getElementById('photoModalImage');
            const link = document.createElement('a');
            link.href = image.src;
            link.download = 'foto-absensi-' + Date.now() + '.jpg';
            link.click();
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt+R to refresh
            if (e.altKey && e.key === 'r') {
                e.preventDefault();
                refreshDashboard();
            }
            // ESC to close modal
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
    
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        // ===== Data Bar Chart =====
        const chartBarData = <?php echo json_encode($chartData, 15, 512) ?>;

        // ===== Donut Chart: Status Hari Ini =====
        const donutData = <?php echo json_encode(array_values($donutData), 15, 512) ?>;
        const donutLabels = ['Hadir','Terlambat','Alpha','Izin','Sakit'];
        const donutColors = ['#22c55e','#f59e0b','#ef4444','#3b82f6','#a855f7'];

        new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderWidth: 2,
                    borderColor: isDark ? '#1f2937' : '#ffffff',
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} siswa`
                        }
                    }
                },
            },
        });

        // ===== Bar Chart: bisa diupdate via AJAX =====
        const barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: labelColor, font: { size: 11 } } },
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: labelColor } },
                y: { grid: { color: gridColor }, ticks: { color: labelColor, stepSize: 1 }, beginAtZero: true },
            },
        };

        function makeBarDatasets(d) {
            return [
                { label: 'Hadir',     data: d.hadir,     backgroundColor: 'rgba(34,197,94,0.8)',  borderRadius: 5 },
                { label: 'Terlambat', data: d.terlambat, backgroundColor: 'rgba(245,158,11,0.8)', borderRadius: 5 },
                { label: 'Alpha',     data: d.alpha,     backgroundColor: 'rgba(239,68,68,0.8)',  borderRadius: 5 },
            ];
        }

        const barChart = new Chart(document.getElementById('chartBar'), {
            type: 'bar',
            data: {
                labels:   chartBarData.labels,
                datasets: makeBarDatasets(chartBarData),
            },
            options: barChartOptions,
        });

        // ===== AJAX: Filter Per Kelas =====
        async function loadChartByClass(classId) {
            const select = document.getElementById('chartClassFilter');
            select.disabled = true;

            try {
                const url = `<?php echo e(route('attendance.dashboard.chart-data')); ?>?class_id=${classId}`;
                const resp = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();

                barChart.data.labels   = data.labels;
                barChart.data.datasets = makeBarDatasets(data);
                barChart.update('active');

                // Update judul subtitle
                const subtitle = document.querySelector('#chartBar').closest('.x-card, div[class*="card"]')
                    ?.querySelector('p.text-xs');
                if (subtitle) {
                    subtitle.textContent = classId
                        ? select.options[select.selectedIndex].text + ' — 7 hari terakhir'
                        : 'Hadir vs Alpha (hari kerja)';
                }
            } catch(e) {
                console.error('Gagal load chart data:', e);
            } finally {
                select.disabled = false;
            }
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

<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/dashboard/index.blade.php ENDPATH**/ ?>