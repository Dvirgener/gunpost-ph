<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Other;

new class extends Component {
    #[Locked]
    public ?Other $others;

    public function mount($others)
    {
        $this->others = $others;
    }
};
?>

<div>
    <div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">

        <flux:heading level="2" class="mb-6 text-black dark:text-white">Full Specifications</flux:heading>

        @if ($this->others)
            acwdcawdc
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Blade / Head Specifications -->
                @php
                    $bladeFields = [
                        'blade_type' => 'Blade Type',
                        'edge_type' => 'Edge Type',
                        'steel_type' => 'Steel Type',
                        'finish' => 'Finish',
                        'full_tang' => 'Full Tang',
                    ];
                @endphp

                @if (collect($bladeFields)->some(fn($_, $field) => $this->others->$field))
                    <div>
                        <flux:heading level="4" class="mb-4">Blade / Head Specifications</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($bladeFields as $field => $label)
                                @if ($this->others->$field)
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                        <span class="font-medium dark:text-white/50">
                                            @if ($field === 'full_tang')
                                                {{ $this->others->$field ? 'Yes' : 'No' }}
                                            @else
                                                {{ ucfirst($this->others->$field) }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Dimensions -->
                @php
                    $dimensionFields = [
                        'blade_length' => 'Blade Length',
                        'head_length' => 'Head Length',
                        'handle_length' => 'Handle Length',
                    ];
                @endphp

                @if (collect($dimensionFields)->some(fn($_, $field) => $this->others->$field) || $this->others->overall_length)
                    <div>
                        <flux:heading level="4" class="mb-4">Dimensions</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->overall_length)
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600 dark:text-white/80">Overall Length:</span>
                                    <span class="font-medium dark:text-white/50">{{ $this->others->overall_length }}
                                        {{ $this->others->length_unit }}</span>
                                </div>
                            @endif
                            @foreach ($dimensionFields as $field => $label)
                                @if ($this->others->$field)
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                        <span class="font-medium dark:text-white/50">{{ $this->others->$field }}
                                            {{ $this->others->length_unit }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Handle / Grip -->
                @php
                    $handleFields = [
                        'handle_material' => 'Handle Material',
                        'handle_color' => 'Handle Color',
                        'grip_texture' => 'Grip Texture',
                    ];
                @endphp

                @if (collect($handleFields)->some(fn($_, $field) => $this->others->$field))
                    <div>
                        <flux:heading level="4" class="mb-4">Handle / Grip</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($handleFields as $field => $label)
                                @if ($this->others->$field)
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                        <span
                                            class="font-medium dark:text-white/50">{{ ucfirst($this->others->$field) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Mechanism & Lock -->
                @php
                    $mechanismFields = [
                        'is_folding' => 'Is Folding',
                        'opening_mechanism' => 'Opening Mechanism',
                        'lock_type' => 'Lock Type',
                    ];
                @endphp

                @if (collect($mechanismFields)->some(fn($_, $field) => $this->others->$field))
                    <div>
                        <flux:heading level="4" class="mb-4">Mechanism & Lock</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($mechanismFields as $field => $label)
                                @if ($this->others->$field)
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                        <span class="font-medium dark:text-white/50">
                                            @if ($field === 'is_folding')
                                                {{ $this->others->$field ? 'Yes' : 'No' }}
                                            @else
                                                {{ ucfirst($this->others->$field) }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Sheath / Scabbard -->
                @php
                    $sheathFields = [
                        'sheath_type' => 'Sheath Type',
                        'carry_type' => 'Carry Type',
                    ];
                @endphp

                @if ($this->others->includes_sheath || collect($sheathFields)->some(fn($_, $field) => $this->others->$field))
                    <div>
                        <flux:heading level="4" class="mb-4">Sheath / Scabbard</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->includes_sheath)
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600 dark:text-white/80">Includes Sheath:</span>
                                    <span
                                        class="font-medium dark:text-white/50">{{ $this->others->includes_sheath ? 'Yes' : 'No' }}</span>
                                </div>
                            @endif
                            @foreach ($sheathFields as $field => $label)
                                @if ($this->others->$field)
                                    <div class="flex justify-between py-2 border-b border-gray-200">
                                        <span class="text-gray-600 dark:text-white/80">{{ $label }}:</span>
                                        <span
                                            class="font-medium dark:text-white/50">{{ ucfirst($this->others->$field) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Packaging -->
                @if ($this->others->has_box || $this->others->has_receipt)
                    <div>
                        <flux:heading level="4" class="mb-4">Packaging</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->has_box)
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600 dark:text-white/80">Has Box:</span>
                                    <span
                                        class="font-medium dark:text-white/50">{{ $this->others->has_box ? 'Yes' : 'No' }}</span>
                                </div>
                            @endif
                            @if ($this->others->has_receipt)
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600 dark:text-white/80">Has Receipt:</span>
                                    <span
                                        class="font-medium dark:text-white/50">{{ $this->others->has_receipt ? 'Yes' : 'No' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Package Includes -->
                @if ($this->others->package_includes)
                    <div class="md:col-span-2">
                        <flux:heading level="4" class="mb-4">Package Includes</flux:heading>
                        <div class="text-sm text-gray-600 dark:text-white/80 bg-gray-50 dark:bg-stone-800 p-4 rounded">
                            {{ $this->others->package_includes }}
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if ($this->others->notes)
                    <div class="md:col-span-2">
                        <flux:heading level="4" class="mb-4">Notes</flux:heading>
                        <div class="text-sm text-gray-600 dark:text-white/80 bg-gray-50 dark:bg-stone-800 p-4 rounded">
                            {{ $this->others->notes }}
                        </div>
                    </div>
                @endif

            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-white/50">No additional details available for this item.</p>
            </div>
        @endif
    </div>
</div>
