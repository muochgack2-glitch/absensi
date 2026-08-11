<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['striped' => false, 'hover' => true]));

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

foreach (array_filter((['striped' => false, 'hover' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $classes = "transition-colors duration-150";
    
    if ($hover) {
        $classes .= " hover:bg-gray-50 dark:hover:bg-gray-800/50";
    }
    
    if ($striped) {
        $classes .= " odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800/30";
    } else {
        $classes .= " bg-white dark:bg-gray-900";
    }
?>

<tr <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php echo e($slot); ?>

</tr>
<?php /**PATH C:\Users\DMCenter\Music\SPMB2\SPMB\absensi\resources\views/components/table/row.blade.php ENDPATH**/ ?>