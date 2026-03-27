@props([
    'label' => null,
])


<div class="flex justify-between items-center py-2 border-b dark:border-gray-600">
    <span class="text-gray-600 dark:text-white/80">
        {{ $label }}:
    </span>
    <span class="font-medium dark:text-white/50 ">
        {{ $slot }}
    </span>
</div>
