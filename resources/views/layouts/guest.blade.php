<x-layouts::app.guest-sidebar :title="$title ?? null">
    <flux:main>
        <div class="flex flex-col justify-between h-full">
            <div>
            {{ $slot }}
            </div>
            <x-virg.footer/>
        </div>

    </flux:main>

</x-layouts::app.guest-sidebar>
