@blaze

@props([
    'label' => '',
])

@php
    $iconMap = [
        'All' => 'squares-2x2',
        'Guns' => 'shield',
        'Airsofts' => 'target',
        'Ammunitions' => 'bolt',
        'Accessories' => 'wrench',
        'Others' => 'cube',
    ];
    $icon = $iconMap[$label] ?? null;
@endphp

<button
    class="p-3 md:text-sm w-full shadow-md shadow-black rounded-md hover:cursor-pointer flex items-center justify-center gap-2 transition-transform hover:scale-105 text-xs outline-1 outline-gray-200">
    @if ($icon)
        <flux:icon :icon="$icon" variant="mini" class="w-4 h-4" />
    @endif
    <span>{{ $label }}</span>
</button>
