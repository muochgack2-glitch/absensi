<!-- Footer Component -->
<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 transition-colors duration-300 mt-8">
    <div class="px-6 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">
            
            <!-- Copyright -->
            <div class="text-sm text-gray-600 dark:text-gray-400 text-center md:text-left">
                <p>&copy; <?php echo e(date('Y')); ?> <span class="font-semibold text-primary-600 dark:text-primary-400"><?php echo e(config('app.name', 'Absensi QR')); ?></span></p>
                <p class="text-xs mt-0.5"><?php echo e($appSchoolName ?? 'Sekolah'); ?> &mdash; All rights reserved</p>
            </div>

            <!-- Info -->
            <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-500">
                <span>
                    <i class="fas fa-code mr-1"></i>
                    Laravel <?php echo e(app()->version()); ?>

                </span>
                <span>
                    <i class="fas fa-server mr-1"></i>
                    PHP <?php echo e(PHP_MAJOR_VERSION); ?>.<?php echo e(PHP_MINOR_VERSION); ?>

                </span>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/layouts/footer.blade.php ENDPATH**/ ?>