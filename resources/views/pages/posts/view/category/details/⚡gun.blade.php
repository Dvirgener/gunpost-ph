<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Gun;

new class extends Component {
    #[Locked]
    public Gun $gun;

    public function mount($gun)
    {
        $this->gun = $gun;
    }
};
?>


<div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">

    <flux:heading level="2" class="mb-6 text-black dark:text-white text-2xl ">Full Specifications</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Basic Info -->
        @php
            $basicFields = [
                'manufacturer' => 'Manufacturer',
                'model' => 'Model',
                'variant' => 'Variant',
                'series' => 'Series',
                'country_of_origin' => 'Country of Origin',
                'platform' => 'Platform',
                'type' => 'Type',
                'action' => 'Action',
            ];
        @endphp

        @if (collect($basicFields)->some(fn($_, $field) => $this->gun->$field))
            <div>
                <flux:heading level="4" class="mb-4 text-blue-500 ">Basic Information
                </flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($basicFields as $field => $label)
                        @if ($this->gun->$field)
                            <x-virg.posts.post-view-prop :label="$label">
                                {{ $this->gun->$field }}
                            </x-virg.posts.post-view-prop>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Ballistics & Capacity -->
        @php
            $ballisticsFields = [
                'caliber' => 'Caliber',
                'capacity' => 'Capacity (rounds)',
                'barrel_length' => 'Barrel Length',
                'overall_length' => 'Overall Length',
            ];
        @endphp

        @if (collect($ballisticsFields)->some(fn($_, $field) => $this->gun->$field))
            <div>
                <flux:heading level="4" class="mb-4 text-blue-500 ">Ballistics & Capacity</flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($ballisticsFields as $field => $label)
                        @if ($this->gun->$field)
                            <x-virg.posts.post-view-prop :label="$label">
                                {{ $this->gun->$field }}
                            </x-virg.posts.post-view-prop>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Dimensions & Weight -->
    @php
        $dimensionFields = [
            'height' => 'Height',
            'width' => 'Width',
            'weight' => 'Weight',
        ];
    @endphp

    @if (collect($dimensionFields)->some(fn($_, $field) => $this->gun->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Dimensions & Weight</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($dimensionFields as $field => $label)
                    <x-virg.posts.post-view-prop :label="$label">
                        {{ $this->gun->$field }}
                    </x-virg.posts.post-view-prop>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Materials & Finish -->
    @php
        $materialFields = [
            'frame_material' => 'Frame Material',
            'slide_material' => 'Slide Material',
            'barrel_material' => 'Barrel Material',
            'finish' => 'Finish',
            'color' => 'Color',
        ];
    @endphp

    @if (collect($materialFields)->some(fn($_, $field) => $this->gun->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Materials & Finish</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($materialFields as $field => $label)
                    @if ($this->gun->$field)
                        <x-virg.posts.post-view-prop :label="$label">
                            {{ $this->gun->$field }}
                        </x-virg.posts.post-view-prop>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Grips & Stock -->
    @php
        $gripFields = [
            'grip_type' => 'Grip Type',
            'stock_type' => 'Stock Type',
            'handguard_type' => 'Handguard Type',
            'rail_type' => 'Rail Type',
        ];
    @endphp

    @if (collect($gripFields)->some(fn($_, $field) => $this->gun->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Grips & Stock</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($gripFields as $field => $label)
                    @if ($this->gun->$field)
                        <x-virg.posts.post-view-prop :label="$label">
                            {{ $this->gun->$field }}
                        </x-virg.posts.post-view-prop>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Sights & Optics -->
    @php
        $sightFields = [
            'sight_type' => 'Sight Type',
            'optic_ready' => 'Optic Ready',
            'optic_mount_pattern' => 'Optic Mount Pattern',
        ];
    @endphp

    @if (collect($sightFields)->some(fn($_, $key) => $key === 'optic_ready' ? $this->gun->$key : $this->gun->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Sights & Optics</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($sightFields as $field => $label)
                    @if ($field === 'optic_ready')
                        @if ($this->gun->$field)
                            <x-virg.posts.post-view-prop :label="$label">
                                <flux:badge class="mt-1" color="green">Yes</flux:badge>
                            </x-virg.posts.post-view-prop>
                        @endif
                    @elseif ($this->gun->$field)
                        <x-virg.posts.post-view-prop :label="$label">
                            {{ $this->gun->$field }}
                        </x-virg.posts.post-view-prop>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Barrel & Muzzle -->
    @php
        $barrelFields = [
            'threaded_barrel' => 'Threaded Barrel',
            'thread_pitch' => 'Thread Pitch',
            'muzzle_device_included' => 'Muzzle Device Included',
            'muzzle_device_type' => 'Muzzle Device Type',
        ];
    @endphp

    @if (collect($barrelFields)->some(fn($_, $key) => $key === 'threaded_barrel' || $key === 'muzzle_device_included'
                ? $this->gun->$key
                : $this->gun->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Barrel & Muzzle</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->gun->threaded_barrel)
                    <x-virg.posts.post-view-prop label="Threaded Barrel">
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->thread_pitch)
                    <x-virg.posts.post-view-prop label="Thread Pitch">
                        {{ $this->gun->thread_pitch }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->muzzle_device_included)
                    <x-virg.posts.post-view-prop label="Muzzle Device Included">
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->muzzle_device_type)
                    <x-virg.posts.post-view-prop label="Muzzle Device Types">
                        {{ $this->gun->muzzle_device_type }}
                    </x-virg.posts.post-view-prop>
                @endif
            </div>
        </div>
    @endif

    <!-- Trigger & Safety -->
    @php
        $triggerFields = [
            'trigger_type' => 'Trigger Type',
            'trigger_pull' => 'Trigger Pull',
            'has_manual_safety' => 'Manual Safety',
            'has_firing_pin_safety' => 'Firing Pin Safety',
        ];
    @endphp

    @if (collect($triggerFields)->some(fn($_, $key) => $key === 'has_manual_safety' || $key === 'has_firing_pin_safety'
                ? $this->gun->$key
                : $this->gun->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Trigger & Safety</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if ($this->gun->trigger_type)
                    <x-virg.posts.post-view-prop label="Trigger Type">
                        {{ ucfirst($this->gun->trigger_type) }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->trigger_pull)
                    <x-virg.posts.post-view-prop label="Trigger Pull">
                        {{ $this->gun->trigger_pull }} {{ $this->gun->trigger_pull_unit }}
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->has_manual_safety)
                    <x-virg.posts.post-view-prop label="Manual Safety">
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
                @if ($this->gun->has_firing_pin_safety)
                    <x-virg.posts.post-view-prop label="Firing Pin Safety">
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </x-virg.posts.post-view-prop>
                @endif
            </div>
        </div>
    @endif

    <!-- Identifiers -->
    @php
        $idFields = [
            'sku' => 'SKU',
            'upc' => 'UPC',
        ];
    @endphp

    @if (collect($idFields)->some(fn($_, $field) => $this->gun->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Identifiers</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($idFields as $field => $label)
                    @if ($this->gun->$field)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                            <span class="font-medium font-mono dark:text-white/50">{{ $this->gun->$field }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Condition & Extras -->
    @php
        $conditionFields = [
            'round_count_estimate' => 'Round Count Estimate',
            'has_box' => 'Original Box',
            'has_receipt' => 'Receipt Included',
            'has_documents' => 'Documentation',
            'document_notes' => 'Document Notes',
            'included_magazines' => 'Included Magazines',
            'included_accessories' => 'Included Accessories',
        ];
    @endphp

    @if (collect($conditionFields)->some(fn($_, $key) => $key === 'has_box' || $key === 'has_receipt' || $key === 'has_documents'
                ? $this->gun->$key
                : $this->gun->$key))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Condition & Extras</flux:heading>
            <div class="gap-4 text-sm grid grid-cols-1 md:grid-cols-2">
                @if ($this->gun->round_count_estimate)
                    <div class="flex justify-between py-2 border-b border-gray-200 items-center">
                        <span class="text-gray-600 dark:text-white/80">Round Count Estimate:</span>
                        <span class="font-medium dark:text-white/50">{{ $this->gun->round_count_estimate }}</span>
                    </div>
                @endif

                @if ($this->gun->included_magazines)
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Included Magazines:</span>
                        <span class="font-medium dark:text-white/50">{{ $this->gun->included_magazines }}</span>
                    </div>
                @endif

                @if ($this->gun->has_box)
                    <div class="py-2 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Original Box:</span>
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </div>
                @endif

                @if ($this->gun->has_receipt)
                    <div class="py-2 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Receipt Included:</span>
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </div>
                @endif



                @if ($this->gun->has_documents)
                    <div class="py-2 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 dark:text-white/80">Documentation:</span>
                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                    </div>
                @endif



                @if ($this->gun->document_notes)
                    <div class="md:col-span-2 py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/50">Document Notes:</span>
                        <p class="text-gray-700 mt-1 dark:text-white/80">{{ $this->gun->document_notes }}
                        </p>
                    </div>
                @endif


                @if ($this->gun->included_accessories)
                    <div class="md:col-span-2 py-2 border-b border-gray-200">
                        <span class="text-gray-600 dark:text-white/80">Included Accessories:</span>
                        <p class="text-gray-700 mt-1 dark:text-white/50">
                            {{ $this->gun->included_accessories }}</p>
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- Notes -->
    @if ($this->gun->notes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-blue-500 ">Additional Notes</flux:heading>
            <p class="text-gray-700 whitespace-pre-wrap dark:text-white/50 text-sm">
                {{ $this->gun->notes }}</p>
        </div>
    @endif
</div>
