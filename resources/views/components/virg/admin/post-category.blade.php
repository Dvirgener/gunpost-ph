@props([
    'category' => 'gun',
])


@switch($category)
    @case('gun')
        <flux:badge color="green" size="sm" inset="top bottom" class="capitalize!">{{ $category }}
        </flux:badge>
    @break

    @case('ammunition')
        <flux:badge color="yellow" size="sm" inset="top bottom" class="capitalize!">{{ $category }}
        </flux:badge>
    @break

    @case('airsoft')
        <flux:badge color="blue" size="sm" inset="top bottom" class="capitalize!">{{ $category }}
        </flux:badge>
    @break

    @case('accessory')
        <flux:badge color="orange" size="sm" inset="top bottom" class="capitalize!">{{ $category }}
        </flux:badge>
    @break

    @case('others')
        <flux:badge color="purple" size="sm" inset="top bottom" class="capitalize!">{{ $category }}
        </flux:badge>
    @break

    @default
@endswitch
