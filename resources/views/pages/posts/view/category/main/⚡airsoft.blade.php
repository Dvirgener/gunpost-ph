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

<div class="text-gray-800">
    <flux:heading level="3" class="mb-4 text-black dark:text-white">Details</flux:heading>
    <div class="space-y-3 text-sm">
        @if ($this->airsoft->platform)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Platform:</span>
                <span class="font-medium dark:text-white/50">{{ ucfirst($this->airsoft->platform) }}</span>
            </div>
        @endif
        @if ($this->airsoft->brand)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Brand:</span>
                <span class="font-medium dark:text-white/50">{{ $this->airsoft->brand }}</span>
            </div>
        @endif
        @if ($this->airsoft->model)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Model:</span>
                <span class="font-medium dark:text-white/50">{{ $this->airsoft->model }}</span>
            </div>
        @endif
        @if ($this->airsoft->power_source)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Power source:</span>
                <span class="font-medium dark:text-white/50">{{ strtoupper($this->airsoft->power_source) }}</span>
            </div>
        @endif
        @if ($this->airsoft->condition)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Condition:</span>
                <span
                    class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->airsoft->condition)) }}</span>
            </div>
        @endif
    </div>
</div>
