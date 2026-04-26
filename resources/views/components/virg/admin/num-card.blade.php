@props([
    'title' => '',
    'number' => 0,
])


<div
    {{ $attributes->merge([
        'class' =>
            'py-5 px-5 rounded-md shadow-lg border-b w-full flex flex-col items-center gap-2 hover:cursor-pointer transition-transform hover:scale-105 dark:bg-gray-800',
    ]) }}>
    <flux:text size="md">{{ $title }}</flux:text>
    <span class="font-bold text-2xl">{{ $number }}</span>
</div>
