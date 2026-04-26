<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="h-full min-h-0">
        <div class="flex h-full min-h-0 flex-col">
            <div class="flex-1 min-h-0">
                {{ $slot }}
            </div>

            <div class="shrink">
                <x-virg.footer />
            </div>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
