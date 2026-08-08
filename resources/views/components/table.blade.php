@props([
    'striped' => true,
    'hoverable' => true
])

@php
    $classes = "min-w-full divide-y divide-gray-200 dark:divide-gray-700";
@endphp

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => $classes]) }}>
        @if(isset($header))
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                {{ $header }}
            </tr>
        </thead>
        @endif
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
