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

<div class="text-gray-800">
    <flux:heading level="3" class="mb-4 text-black dark:text-white">Details</flux:heading>
    <div class="space-y-3 text-sm">
        @if ($this->gun->platform)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Platform:</span>
                <span class="font-medium dark:text-white/50">{{ ucfirst($this->gun->platform) }}</span>
            </div>
        @endif

        @if ($this->gun->manufacturer)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Manufacturer:</span>
                <span class="font-medium dark:text-white/50">{{ $this->gun->manufacturer }}</span>
            </div>
        @endif

        @if ($this->gun->model)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-white">Model:</span>
                <span class="font-medium dark:text-white/50">{{ $this->gun->model }}</span>
            </div>
        @endif

        @if ($this->gun->caliber)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-white">Caliber:</span>
                <span class="font-medium dark:text-white/50">{{ $this->gun->caliber }}</span>
            </div>
        @endif

        @if ($this->gun->condition)
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-white">Condition:</span>
                <span
                    class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->gun->condition)) }}</span>
            </div>
        @endif


    </div>
</div>
