<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'fa-inbox',
    'title' => 'Tidak ada data',
    'message' => 'Belum ada data yang tersedia saat ini.',
    'action' => null,
    'actionText' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'icon' => 'fa-inbox',
    'title' => 'Tidak ada data',
    'message' => 'Belum ada data yang tersedia saat ini.',
    'action' => null,
    'actionText' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
        <i class="fas <?php echo e($icon); ?> text-3xl text-gray-400 dark:text-gray-600"></i>
    </div>
    
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2"><?php echo e($title); ?></h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mb-6"><?php echo e($message); ?></p>
    
    <?php if($action && $actionText): ?>
        <a href="<?php echo e($action); ?>" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-medium rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 hover:-translate-y-0.5 shadow-md hover:shadow-lg">
            <?php echo e($actionText); ?>

        </a>
    <?php endif; ?>
    
    <?php echo e($slot); ?>

</div>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/components/empty-state.blade.php ENDPATH**/ ?>