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

<div class="text-gray-800">
    <flux:heading level="3" class="mb-4 text-black dark:text-white">Details</flux:heading>
    <div class="space-y-3 text-sm">
        @if ($this->accessory && $this->accessory->category)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Category:</span>
                <span class="font-medium dark:text-white/50">{{ ucfirst($this->accessory->category) }}</span>
            </div>
        @endif

        @if ($this->accessory && $this->accessory->brand)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Brand:</span>
                <span class="font-medium dark:text-white/50">{{ $this->accessory->brand }}</span>
            </div>
        @endif

        @if ($this->accessory && $this->accessory->model)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Model:</span>
                <span class="font-medium dark:text-white/50">{{ $this->accessory->model }}</span>
            </div>
        @endif

        @if ($this->accessory && $this->accessory->compatible_with)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Compatible With:</span>
                <span class="font-medium dark:text-white/50">{{ $this->accessory->compatible_with }}</span>
            </div>
        @endif


    </div>
    {{-- @if ($this->accessory)
        <p class="text-gray-600 dark:text-white/50 text-sm">Accessory details not provided.</p>
    @else
        <p class="text-gray-600 dark:text-white/50 text-sm">No accessory details available.</p>
    @endif --}}
</div>
