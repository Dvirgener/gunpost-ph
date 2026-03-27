<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Accessory;

new class extends Component {
    #[Locked]
    public ?Accessory $accessory;

    public function mount($accessory)
    {
        $this->accessory = $accessory;
    }
};
?>

<div>
    <div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">

        <flux:heading level="2" class="mb-6 text-black dark:text-white">Full Specifications</flux:heading>

        @if ($this->accessory)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">


                <!-- Materials & Finish -->
                @php
                    $materialFields = [
                        'color' => 'Color',
                        'material' => 'Material',
                    ];
                @endphp

                @if (collect($materialFields)->some(fn($_, $field) => $this->accessory->$field))
                    <div>
                        <flux:heading level="4" class="mb-4 text-blue-500">Materials & Finish</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($materialFields as $field => $label)
                                @if ($this->accessory->$field)
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ $this->accessory->$field }}
                                    </x-virg.posts.post-view-prop>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Compatibility & Fitment -->
                @php
                    $compatibilityFields = [
                        'mount_type' => 'Mount Type',
                        'size' => 'Size',
                    ];
                @endphp

                @if (collect($compatibilityFields)->some(fn($_, $field) => $this->accessory->$field))
                    <div>
                        <flux:heading level="4" class="mb-4 text-blue-500">Compatibility & Fitment</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($compatibilityFields as $field => $label)
                                @if ($this->accessory->$field)
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ $this->accessory->$field }}
                                    </x-virg.posts.post-view-prop>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>



            <!-- Commercial Identifiers -->
            @php
                $idFields = [
                    'sku' => 'SKU',
                    'upc' => 'UPC',
                ];
            @endphp

            @if (collect($idFields)->some(fn($_, $field) => $this->accessory->$field))
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <flux:heading level="4" class="mb-4 text-blue-500">Commercial Identifiers</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        @foreach ($idFields as $field => $label)
                            @if ($this->accessory->$field)
                                <x-virg.posts.post-view-prop :label="$label">
                                    {{ $this->accessory->$field }}
                                </x-virg.posts.post-view-prop>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Package Contents -->
            @if ($this->accessory->package_includes)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <flux:heading level="4" class="mb-4 text-blue-500">Package Contents</flux:heading>
                    <p class="text-gray-700 whitespace-pre-wrap dark:text-white/50 text-sm">
                        {{ $this->accessory->package_includes }}</p>
                </div>
            @endif

            <!-- Notes -->
            @if ($this->accessory->notes)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <flux:heading level="4" class="mb-4 text-blue-500">Additional Notes</flux:heading>
                    <p class="text-gray-700 whitespace-pre-wrap dark:text-white/50 text-sm">
                        {{ $this->accessory->notes }}</p>
                </div>
            @endif
        @else
            <p class="text-gray-600 dark:text-white/50">Accessory details not provided.</p>
        @endif
    </div>


</div>
