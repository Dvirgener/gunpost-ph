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
                        <flux:heading level="4" class="mb-4 text-blue-500">Blade / Head Specifications</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($bladeFields as $field => $label)
                                @if ($this->others->$field)
                                    @if ($field === 'full_tang')
                                        <x-virg.posts.post-view-prop :label="$label">
                                            <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                                        </x-virg.posts.post-view-prop>
                                    @else
                                        <x-virg.posts.post-view-prop :label="$label">
                                            {{ ucfirst($this->others->$field) }}
                                        </x-virg.posts.post-view-prop>
                                    @endif
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
                        <flux:heading level="4" class="mb-4 text-blue-500">Dimensions</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->overall_length)
                                <x-virg.posts.post-view-prop label="Overall Length">
                                    {{ $this->others->overall_length }}
                                    {{ $this->others->length_unit }}
                                </x-virg.posts.post-view-prop>
                            @endif
                            @foreach ($dimensionFields as $field => $label)
                                @if ($this->others->$field)
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ $this->others->$field }}
                                        {{ $this->others->length_unit }}
                                    </x-virg.posts.post-view-prop>
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
                        <flux:heading level="4" class="mb-4 text-blue-500">Handle / Grip</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($handleFields as $field => $label)
                                @if ($this->others->$field)
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ ucfirst($this->others->$field) }}
                                    </x-virg.posts.post-view-prop>
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
                        <flux:heading level="4" class="mb-4 text-blue-500">Mechanism & Lock</flux:heading>
                        <div class="space-y-3 text-sm">
                            @foreach ($mechanismFields as $field => $label)
                                @if ($field === 'is_folding')
                                    <x-virg.posts.post-view-prop :label="$label">
                                        <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                                    </x-virg.posts.post-view-prop>
                                @else
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ ucfirst($this->others->$field) }}
                                    </x-virg.posts.post-view-prop>
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
                        <flux:heading level="4" class="mb-4 text-blue-500">Sheath / Scabbard</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->includes_sheath)
                                <x-virg.posts.post-view-prop label="Includes Sheath">
                                    <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                                </x-virg.posts.post-view-prop>
                            @endif
                            @foreach ($sheathFields as $field => $label)
                                @if ($this->others->$field)
                                    <x-virg.posts.post-view-prop :label="$label">
                                        {{ ucfirst($this->others->$field) }}
                                    </x-virg.posts.post-view-prop>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Packaging -->
                @if ($this->others->has_box || $this->others->has_receipt)
                    <div>
                        <flux:heading level="4" class="mb-4 text-blue-500">Packaging</flux:heading>
                        <div class="space-y-3 text-sm">
                            @if ($this->others->has_box)
                                <x-virg.posts.post-view-prop label="Has Box">
                                    <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                                </x-virg.posts.post-view-prop>
                            @endif
                            @if ($this->others->has_receipt)
                                <x-virg.posts.post-view-prop label="Has Receipt">
                                    <flux:badge class="mt-1 px-4!" color="green">Yes</flux:badge>
                                </x-virg.posts.post-view-prop>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Package Includes -->
                @if ($this->others->package_includes)
                    <div class="md:col-span-2">
                        <flux:heading level="4" class="mb-4 text-blue-500">Package Includes</flux:heading>
                        <div class="text-sm text-gray-600 dark:text-white/80  p-4 rounded">
                            {{ $this->others->package_includes }}
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if ($this->others->notes)
                    <div class="md:col-span-2">
                        <flux:heading level="4" class="mb-4 text-blue-500">Notes</flux:heading>
                        <div class="text-sm text-gray-600 dark:text-white/80  p-4 rounded">
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
