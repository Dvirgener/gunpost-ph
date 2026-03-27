<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Airsoft;

new class extends Component {
    #[Locked]
    public Airsoft $airsoft;

    public function mount($airsoft)
    {
        $this->airsoft = $airsoft;
    }
};
?>

<div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">

    <flux:heading level="2" class="mb-6 text-black dark:text-white">Full Specifications</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Basic Info -->
        @php
            $basicFields = [
                'brand' => 'Brand',
                'model' => 'Model',
                'series' => 'Series',
            ];
        @endphp

        @if (collect($basicFields)->some(fn($_, $field) => $this->airsoft->$field))
            <div>
                <flux:heading level="4" class="mb-4 text-blue-500">Basic Information</flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($basicFields as $field => $label)
                        @if ($this->airsoft->$field)
                            <x-virg.posts.post-view-prop :label="$label">
                                {{ $this->airsoft->$field }}
                            </x-virg.posts.post-view-prop>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Classification -->
        @php
            $classificationFields = [
                'platform' => 'Platform',
                'power_source' => 'Power Source',
                'compatibility_platform' => 'Compatibility Platform',
                'gearbox_version' => 'Gearbox Version',
            ];
        @endphp

        @if (collect($classificationFields)->some(fn($_, $field) => $this->airsoft->$field))
            <div>
                <flux:heading level="4" class="mb-4 text-blue-500">Classification</flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($classificationFields as $field => $label)
                        @if ($this->airsoft->$field)
                            <x-virg.posts.post-view-prop :label="$label">
                                {{ ucfirst(str_replace('_', ' ', $this->airsoft->$field)) }}
                            </x-virg.posts.post-view-prop>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Performance -->
    @php
        $performanceFields = [
            'fps' => 'FPS',
            'joule' => 'Joule',
        ];
    @endphp

    @if (collect($performanceFields)->some(fn($_, $field) => $this->airsoft->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Performance</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($performanceFields as $field => $label)
                    @if ($this->airsoft->$field)
                        <x-virg.posts.post-view-prop :label="$label">
                            {{ number_format($this->airsoft->$field, 2) }}
                        </x-virg.posts.post-view-prop>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Build -->
    @php
        $buildFields = [
            'color' => 'Color',
            'body_material' => 'Body Material',
            'metal_body' => 'Metal Body',
            'blowback' => 'Blowback',
        ];
    @endphp

    @if (collect($buildFields)->some(fn($_, $key) => $key === 'metal_body' || $key === 'blowback' ? $this->airsoft->$key : $this->airsoft->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Build</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->airsoft->color)
                    <x-virg.posts.post-view-prop label="Color">
                        {{ ucfirst($this->airsoft->color) }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->body_material)
                    <x-virg.posts.post-view-prop label="Body Material">
                        {{ ucfirst($this->airsoft->body_material) }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->metal_body)
                    <x-virg.posts.post-view-prop label="Metal Body">
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->blowback)
                    <x-virg.posts.post-view-prop label="Blowback">
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
            </div>
        </div>
    @endif

    <!-- Power -->
    @php
        $powerFields = [
            'battery_type' => 'Battery Type',
            'battery_connector' => 'Battery Connector',
            'gas_type' => 'Gas Type',
        ];
    @endphp

    @if (collect($powerFields)->some(fn($_, $field) => $this->airsoft->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Power</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($powerFields as $field => $label)
                    @if ($this->airsoft->$field)
                        <x-virg.posts.post-view-prop :label="$label">
                            {{ ucfirst($this->airsoft->$field) }}
                        </x-virg.posts.post-view-prop>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Magazine -->
    @php
        $magazineFields = [
            'includes_magazines' => 'Includes Magazines',
            'magazine_count' => 'Magazine Count',
            'magazine_type' => 'Magazine Type',
        ];
    @endphp

    @if (collect($magazineFields)->some(fn($_, $key) => $key === 'includes_magazines' ? $this->airsoft->$key : $this->airsoft->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Magazine</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->airsoft->includes_magazines)
                    <x-virg.posts.post-view-prop label="Includes Magazines">
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->magazine_count)
                    <x-virg.posts.post-view-prop label="Magazine Count">
                        {{ $this->airsoft->magazine_count }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->magazine_type)
                    <x-virg.posts.post-view-prop label="Magazine Type">
                        {{ ucfirst($this->airsoft->magazine_type) }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->airsoft->magazine_count)
                    <x-virg.posts.post-view-prop label="Magazine Count">
                        {{ $this->airsoft->magazine_count }}
                    </x-virg.posts.post-view-prop>
                @endif
            </div>
        </div>
    @endif

    <!-- Package -->
    @if ($this->airsoft->package_includes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Package</flux:heading>
            <div class="text-sm">
                <p class="text-gray-600 dark:text-white/80 mb-2">Package Includes:</p>
                <p class="font-medium dark:text-white/50">{{ $this->airsoft->package_includes }}</p>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if ($this->airsoft->notes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500">Notes</flux:heading>
            <div class="text-sm">
                <p class="text-gray-600 dark:text-white/80 mb-2">Additional Notes:</p>
                <p class="font-medium dark:text-white/50">{{ $this->airsoft->notes }}</p>
            </div>
        </div>
    @endif

</div>
