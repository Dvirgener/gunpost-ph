@props([
    'amount' => 0,
])

<span>{{ '₱ ' . number_format((float) $amount, 2, '.', ',') }}</span>
