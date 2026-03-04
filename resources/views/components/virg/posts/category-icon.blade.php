@props([
    'cat' => '',
])

@php
    $iconMap = [
        'gun' => 'shield',
        'airsoft' => 'target',
        'ammunition' => 'bolt',
        'accessory' => 'wrench',
        'other' => 'cube',
    ];
    $icon = $iconMap[$cat] ?? null;
@endphp

<flux:icon :icon="$icon" variant="mini" class="w-4 h-4" />
