<?php
    $pageTitle = 'QR Code - ' . $student->nama;
    $breadcrumbs = [
        ['label' => 'Data Siswa', 'url' => route('attendance.students.index')],
        ['label' => $student->nama, 'url' => route('attendance.students.show', $student)],
        ['label' => 'QR Code']
    ];
?>

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
    <div class="max-w-2xl mx-auto space-y-6">
        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'print:shadow-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'print:shadow-none']); ?>
            
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center text-white text-3xl mx-auto mb-4">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <?php echo e(\App\Models\AttendanceSetting::get('school_name', 'SMK Negeri 1')); ?>

                </h1>
                <p class="text-gray-600 dark:text-gray-400">Sistem Absensi Siswa</p>
            </div>

            
            <div class="border-t-2 border-gray-200 dark:border-gray-700 my-8"></div>

            
            <div class="text-center mb-8">
                <?php if($student->foto_profil): ?>
                <div class="mb-6">
                    <img 
                        src="<?php echo e(Storage::url($student->foto_profil)); ?>" 
                        alt="<?php echo e($student->nama); ?>"
                        class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-primary-500 shadow-lg"
                    >
                </div>
                <?php endif; ?>
                
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo e($student->nama); ?></h2>
                <div class="flex items-center justify-center gap-4 text-gray-600 dark:text-gray-400">
                    <span class="flex items-center">
                        <i class="fas fa-id-card mr-2 text-primary-500"></i>
                        NIS: <?php echo e($student->nis); ?>

                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-school mr-2 text-primary-500"></i>
                        <?php echo e($student->kelas->nama_kelas ?? '-'); ?>

                    </span>
                </div>
            </div>

            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 p-8 rounded-2xl border-4 border-primary-500 shadow-inner">
                <?php if($student->qr_code_path && Storage::disk('public')->exists($student->qr_code_path)): ?>
                    <div class="flex justify-center">
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <?php echo Storage::disk('public')->get($student->qr_code_path); ?>

                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">QR Code Belum Dibuat</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Silakan generate QR Code terlebih dahulu</p>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="mt-8 print:hidden bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-l-4 border-blue-500 rounded-lg p-6">
                <h3 class="font-bold text-blue-900 dark:text-blue-300 mb-4 flex items-center text-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    Cara Penggunaan
                </h3>
                <ol class="space-y-3 text-blue-800 dark:text-blue-200">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                        <span>Tunjukkan QR Code ini pada scanner saat absensi</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                        <span>Pastikan QR Code terlihat jelas dan tidak terlipat</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                        <span>Tunggu konfirmasi dari sistem setelah scan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">4</span>
                        <span>Simpan QR Code ini dengan baik untuk absensi harian</span>
                    </li>
                </ol>
            </div>

            
            <div class="hidden print:block mt-8 text-center text-sm text-gray-500">
                Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?>

            </div>

            
            <div class="mt-8 print:hidden space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <a 
                        href="<?php echo e(route('attendance.qr.download', $student->nis)); ?>" 
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-download mr-2"></i>
                        Download PNG
                    </a>
                    
                    <button 
                        onclick="window.print()" 
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-print mr-2"></i>
                        Print QR Code
                    </button>
                </div>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('regenerate-qr')): ?>
                <form 
                    action="<?php echo e(route('attendance.qr.regenerate', $student->nis)); ?>" 
                    method="POST"
                    onsubmit="return confirm('Yakin ingin regenerate QR Code? QR Code lama akan diganti.')"
                >
                    <?php echo csrf_field(); ?>
                    <button 
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white hover:from-yellow-600 hover:to-yellow-700 shadow-md hover:shadow-lg"
                    >
                        <i class="fas fa-redo mr-2"></i>
                        Regenerate QR Code
                    </button>
                </form>
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

        
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'print:hidden']); ?>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">💡 Tips</h3>
                    <p class="text-gray-700 dark:text-gray-300 text-sm">
                        Simpan gambar QR Code ini di ponsel atau print untuk kemudahan absensi harian. 
                        QR Code ini unik untuk setiap siswa dan tidak boleh dibagikan ke orang lain.
                    </p>
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

    <?php $__env->startPush('styles'); ?>
    <style>
        @media print {
            .print\:hidden {
                display: none !important;
            }
            .print\:block {
                display: block !important;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Auto-print functionality for mobile
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_print') === '1') {
            window.onload = function() {
                setTimeout(() => {
                    window.print();
                }, 500);
            };
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
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/attendance/qr/show.blade.php ENDPATH**/ ?>