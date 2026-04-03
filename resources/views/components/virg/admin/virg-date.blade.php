@props(['date', 'withTime' => true])



<span>
    @switch($withTime)
        @case(true)
            {{ $date->timezone('Asia/Manila')->format('M d, Y h:m A') ?? '—' }}
        @break

        @case(false)
            {{ $date->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}
        @break

        @default
    @endswitch

</span>
