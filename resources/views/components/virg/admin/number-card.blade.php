@props([
    'label' => '',
    'numbers' => 10,
    'link' => '#',
])

<a href="{{ $link }}" aria-label="Latest on our blog">
    <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
        <flux:heading class="flex items-center gap-2 uppercase">{{ $label }}
            <flux:icon name="arrow-up-right" class="ml-auto text-zinc-400" variant="micro" />
        </flux:heading>
        <flux:text class="mt-2">
            <p class="text-4xl text-start">{{ $numbers }}</p>
        </flux:text>
    </flux:card>
</a>
