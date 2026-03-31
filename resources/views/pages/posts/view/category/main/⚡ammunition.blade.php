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

<div class="text-gray-800">
    <flux:heading level="3" class="mb-4 text-black dark:text-white">Details</flux:heading>
    <div class="space-y-3 text-sm">
        @if ($this->ammunition->caliber)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Caliber:</span>
                <span class="font-medium dark:text-white/50">{{ $this->ammunition->caliber }}</span>
            </div>
        @endif

        @if ($this->ammunition->brand)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Brand:</span>
                <span class="font-medium dark:text-white/50">{{ $this->ammunition->brand }}</span>
            </div>
        @endif

        @if ($this->ammunition->bullet_type)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Bullet Type:</span>
                <span class="font-medium dark:text-white/50">{{ ucfirst($this->ammunition->bullet_type) }}</span>
            </div>
        @endif

        @if ($this->ammunition->grain)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Grain:</span>
                <span class="font-medium dark:text-white/50">{{ $this->ammunition->grain }}</span>
            </div>
        @endif

        @if ($this->ammunition->condition)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Condition:</span>
                <span
                    class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->ammunition->condition)) }}</span>
            </div>
        @endif

        @if ($this->ammunition->total_rounds)
            <div class="flex justify-between">
                <span class="text-black dark:text-white">Total Rounds:</span>
                <span class="font-medium dark:text-white/50">{{ $this->ammunition->total_rounds }}</span>
            </div>
        @endif

    </div>
</div>
