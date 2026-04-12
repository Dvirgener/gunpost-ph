<div
    class="w-full flex flex-col md:flex-row text-center justify-center mt-5 items-center gap-2 rounded-2xl ring-2 ring-gray-500/50 shadow-lg shadow-gray-500/50  py-1 dark:text-white">
    @php
        $isMobile = (new \Jenssegers\Agent\Agent())->isMobile();

    @endphp

    @if (!$isMobile)
            <a href=" https://www.linxcodes.com" target="_blank" rel="noopener noreferrer">
        <img src=" {{ asset('/Linx Codes Final Logo.png') }}" alt="My Logo" class="w-15 h-15 border rounded-full">
    </a>
    <p class=" font-bold text-xs ">© {{ now()->year }} Designed by Virgener @
        linxcodes.com
    </p>

    <p class=" font-bold text-xs ">All Rights
        reserved. </p>
    @else
    {{-- Main Logo --}}
    <div class="w-full flex justify-center">
        <div class="w-40">
            <x-virg.logo />
        </div>
    </div>
    {{-- Main Logo --}}
    @endif

</div>
