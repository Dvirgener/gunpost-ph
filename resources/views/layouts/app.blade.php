<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        <div class="flex flex-col justify-between h-full">
            <div class="h-full">
            {{ $slot }}
            </div>
            <x-virg.footer/>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
