<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Ammunition;

new class extends Component {
    #[Locked]
    public Ammunition $ammunition;

    public function mount($ammunition)
    {
        $this->ammunition = $ammunition;
    }
};
?>


<div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">

    <flux:heading level="2" class="mb-6 text-black dark:text-white">Full Specifications</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Basic Identification -->
        @php
            $basicFields = [
                'brand' => 'Brand',
                'product_line' => 'Product Line',
                'caliber' => 'Caliber',
                'bullet_type' => 'Bullet Type',
                'grain' => 'Grain',
            ];
        @endphp

        @if (collect($basicFields)->some(fn($_, $field) => $this->ammunition->$field))
            <div>
                <flux:heading level="4" class="mb-4 text-black dark:text-white">Basic Information
                </flux:heading>
                <div class="space-y-3 text-sm">
                    @foreach ($basicFields as $field => $label)
                        @if ($this->ammunition->$field)
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                <span
                                    class="font-medium dark:text-white/50 ">{{ ucfirst($this->ammunition->$field) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Ammunition Properties -->
        @php
            $ammoFields = [
                'case_material' => 'Case Material',
                'primer_type' => 'Primer Type',
                'corrosive' => 'Corrosive',
                'reloads' => 'Reloads',
            ];
        @endphp

        @if (collect($ammoFields)->some(fn($_, $key) => $key === 'corrosive' || $key === 'reloads' ? $this->ammunition->$key : $this->ammunition->$key))
            <div>
                <flux:heading level="4" class="mb-4 text-black dark:text-white">Ammunition Properties
                </flux:heading>
                <div class="space-y-3 text-sm">
                    @if ($this->ammunition->case_material)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">Case Material:</span>
                            <span
                                class="font-medium dark:text-white/50">{{ ucfirst($this->ammunition->case_material) }}</span>
                        </div>
                    @endif
                    @if ($this->ammunition->primer_type)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">Primer Type:</span>
                            <span
                                class="font-medium dark:text-white/50">{{ ucfirst($this->ammunition->primer_type) }}</span>
                        </div>
                    @endif
                    @if ($this->ammunition->corrosive)
                        <div class="py-2 border-b border-gray-200 flex justify-between items-center">
                            <span class="text-gray-600 dark:text-white/80">Corrosive:</span>
                            <flux:badge class="mt-1" color="red">Yes</flux:badge>
                        </div>
                    @endif
                    @if ($this->ammunition->reloads)
                        <div class="py-2 border-b border-gray-200 flex justify-between items-center">
                            <span class="text-gray-600 dark:text-white/80">Reloads:</span>
                            <flux:badge class="mt-1" color="blue">Yes</flux:badge>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- Quantity & Packaging -->
    @php
        $quantityFields = [
            'total_rounds' => 'Total Rounds',
            'boxes' => 'Number of Boxes',
            'rounds_per_box' => 'Rounds Per Box',
        ];
    @endphp

    @if (collect($quantityFields)->some(fn($_, $field) => $this->ammunition->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Quantity & Packaging</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($quantityFields as $field => $label)
                    @if ($this->ammunition->$field)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                            <span class="font-medium dark:text-white/50">{{ $this->ammunition->$field }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Lot & SKU Information -->
    @php
        $lotFields = [
            'lot_number' => 'Lot Number',
            'sku' => 'SKU',
            'upc' => 'UPC',
        ];
    @endphp

    @if (collect($lotFields)->some(fn($_, $field) => $this->ammunition->$field))
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Lot & SKU Information</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach ($lotFields as $field => $label)
                    @if ($this->ammunition->$field)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                            <span
                                class="font-medium font-mono dark:text-white/50">{{ $this->ammunition->$field }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Condition -->
    @if ($this->ammunition->condition)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Condition</flux:heading>
            <div class="text-sm">
                <div class="flex justify-between py-2">
                    <span class="text-gray-600 dark:text-white/80">Status:</span>
                    <span class="font-medium dark:text-white/50">
                        @php
                            $conditionColors = [
                                'factory_new' => 'green',
                                'sealed' => 'blue',
                                'opened' => 'yellow',
                                'mixed' => 'orange',
                                'other' => 'gray',
                            ];
                            $color = $conditionColors[$this->ammunition->condition] ?? 'gray';
                        @endphp
                        <flux:badge color="{{ $color }}">
                            {{ ucfirst(str_replace('_', ' ', $this->ammunition->condition)) }}
                        </flux:badge>
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if ($this->ammunition->notes)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <flux:heading level="4" class="mb-4 text-black dark:text-white">Additional Notes</flux:heading>
            <p class="text-gray-700 whitespace-pre-wrap dark:text-white/50 text-sm">
                {{ $this->ammunition->notes }}</p>
        </div>
    @endif
</div>
