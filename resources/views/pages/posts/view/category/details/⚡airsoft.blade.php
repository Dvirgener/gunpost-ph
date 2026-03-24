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
                <flux:heading level="4" class="mb-4 text-black dark:text-white">Basic Information</flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($basicFields as $field => $label)
                        @if ($this->airsoft->$field)
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                <span class="font-medium dark:text-white/50">{{ $this->airsoft->$field }}</span>
                            </div>
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
                <flux:heading level="4" class="mb-4 text-black dark:text-white">Classification</flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($classificationFields as $field => $label)
                        @if ($this->airsoft->$field)
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                <span
                                    class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->airsoft->$field)) }}</span>
                            </div>
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
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Performance</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($performanceFields as $field => $label)
                    @if ($this->airsoft->$field)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                            <span class="font-medium dark:text-white/50">
                                @if ($field === 'joule')
                                    {{ number_format($this->airsoft->$field, 2) }}
                                @else
                                    {{ $this->airsoft->$field }}
                                @endif
                            </span>
                        </div>
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
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Build</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->airsoft->color)
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Color:</span>
                        <span class="font-medium dark:text-white/50">{{ ucfirst($this->airsoft->color) }}</span>
                    </div>
                @endif
                @if ($this->airsoft->body_material)
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Body Material:</span>
                        <span class="font-medium dark:text-white/50">{{ ucfirst($this->airsoft->body_material) }}</span>
                    </div>
                @endif
                @if ($this->airsoft->metal_body)
                    <div class="py-2 border-b border-gray-200 gap-3 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Metal Body:</span>
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </div>
                @endif
                @if ($this->airsoft->blowback)
                    <div class="py-2 border-b border-gray-200 gap-3 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Blowback:</span>
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </div>
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
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Power</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($powerFields as $field => $label)
                    @if ($this->airsoft->$field)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                            <span class="font-medium dark:text-white/50">{{ ucfirst($this->airsoft->$field) }}</span>
                        </div>
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
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Magazine</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->airsoft->includes_magazines)
                    <div class="py-2 border-b border-gray-200 gap-3 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Includes Magazines:</span>
                        <flux:badge class="mt-1" color="green">Yes</flux:badge>
                    </div>
                @endif
                @if ($this->airsoft->magazine_count)
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Magazine Count:</span>
                        <span class="font-medium dark:text-white/50">{{ $this->airsoft->magazine_count }}</span>
                    </div>
                @endif
                @if ($this->airsoft->magazine_type)
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Magazine Type:</span>
                        <span
                            class="font-medium dark:text-white/50">{{ ucfirst($this->airsoft->magazine_type) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Package -->
    @if ($this->airsoft->package_includes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Package</flux:heading>
            <div class="text-sm">
                <p class="text-gray-600 dark:text-white/80 mb-2">Package Includes:</p>
                <p class="font-medium dark:text-white/50">{{ $this->airsoft->package_includes }}</p>
            </div>
        </div>
    @endif

    <!-- Condition -->
    @if ($this->airsoft->condition)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Condition</flux:heading>
            <div class="text-sm">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-white/80">Condition:</span>
                    <span
                        class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->airsoft->condition)) }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if ($this->airsoft->notes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Notes</flux:heading>
            <div class="text-sm">
                <p class="text-gray-600 dark:text-white/80 mb-2">Additional Notes:</p>
                <p class="font-medium dark:text-white/50">{{ $this->airsoft->notes }}</p>
            </div>
        </div>
    @endif

</div>
