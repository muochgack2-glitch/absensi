<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['sortable' => false, 'direction' => null]));

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

foreach (array_filter((['sortable' => false, 'direction' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = "px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider";
    
    if ($sortable) {
        $classes .= " cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 select-none";
    }
?>

<th <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <div class="flex items-center space-x-1">
        <span><?php echo e($slot); ?></span>
        
        <?php if($sortable): ?>
            <div class="flex flex-col">
                <i class="fas fa-caret-up text-xs <?php echo e($direction === 'asc' ? 'text-primary-500' : 'text-gray-400'); ?> -mb-1"></i>
                <i class="fas fa-caret-down text-xs <?php echo e($direction === 'desc' ? 'text-primary-500' : 'text-gray-400'); ?>"></i>
            </div>
        <?php endif; ?>
    </div>
</th>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/components/table/header.blade.php ENDPATH**/ ?>