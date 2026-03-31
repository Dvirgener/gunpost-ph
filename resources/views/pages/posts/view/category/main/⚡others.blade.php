<?php

use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Models\posts\categories\Other;

new class extends Component {
    #[Locked]
    public ?Other $others;

    public function mount($other)
    {
        $this->others = $other;
    }
};
?>

<div class="text-gray-800">
    <flux:heading level="3" class="mb-4 text-black dark:text-white">Details</flux:heading>
    <div class="space-y-3 text-sm">
        @if ($this->others && $this->others->weapon_type)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Weapon Type:</span>
                <span class="font-medium dark:text-white/50">{{ ucfirst($this->others->weapon_type) }}</span>
            </div>
        @endif


        @if ($this->others && $this->others->brand)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Brand:</span>
                <span class="font-medium dark:text-white/50">{{ $this->others->brand }}</span>
            </div>
        @endif

        @if ($this->others && $this->others->model)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Model:</span>
                <span class="font-medium dark:text-white/50">{{ $this->others->model }}</span>
            </div>
        @endif

        @if ($this->others && $this->others->variant)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Variant:</span>
                <span class="font-medium dark:text-white/50">{{ $this->others->variant }}</span>
            </div>
        @endif



    </div>
</div>
