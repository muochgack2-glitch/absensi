<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => '',
    'subtitle' => '',
    'actions' => null
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
    'title' => '',
    'subtitle' => '',
    'actions' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <?php if($title || $actions): ?>
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($title); ?></h3>
                    <?php if($subtitle): ?>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e($subtitle); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if($actions): ?>
                    <div class="flex items-center space-x-2">
                        <?php echo e($actions); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="p-6">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/components/section-card.blade.php ENDPATH**/ ?>