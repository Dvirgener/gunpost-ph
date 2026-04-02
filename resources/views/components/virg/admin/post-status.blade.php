@props([
    'status' => 'draft',
])


@switch($status)
    @case('pending')
        <flux:badge color="yellow" size="sm" inset="top bottom" class="capitalize!">{{ $status }}
        </flux:badge>
    @break

    @case('approved')
        <flux:badge color="green" size="sm" inset="top bottom" class="capitalize!">{{ $status }}
        </flux:badge>
    @break

    @case('expired')
        <flux:badge color="red" size="sm" inset="top bottom" class="capitalize!">{{ $status }}
        </flux:badge>
    @break

    @case('closed')
        <flux:badge color="red" size="sm" inset="top bottom" class="capitalize!">{{ $status }}
        </flux:badge>
    @break

    @default
@endswitch
