@props([
    'name' => '',
    'link' => '#'
])
    <a href="{{ $link }}">
        <div class="p-5 hover:scale-110 transition-all rounded-lg w-40 h-25 flex justify-center items-center font-bold shadow-lg bg-white text-black dark:bg-stone-900 dark:text-white dark:shadow-stone-700">
            {{ $name }}
        </div>
    </a>
