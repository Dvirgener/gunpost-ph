<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;

new class extends Component {
    public $activeImagePath = null;
    public $activeImageIndex = 0;
    public $allPhotos = [];

    protected $listeners = ['openPhotoModal'];

    public function mount($photos = [])
    {
        $this->allPhotos = $photos;
    }

    #[On('openPhotoModalEvent')]
    public function openPhoto($index)
    {
        $this->activeImagePath = $this->allPhotos[$index['index']] ?? null;
        $this->activeImageIndex = $index['index'];
    }

    public function navigatePhoto($direction)
    {
        if ($direction === 'next') {
            $this->activeImageIndex = ($this->activeImageIndex % count($this->allPhotos)) + 1;
        } elseif ($direction === 'prev') {
            $this->activeImageIndex = (($this->activeImageIndex - 2 + count($this->allPhotos)) % count($this->allPhotos)) + 1;
        }
        $this->activeImagePath = $this->allPhotos[$this->activeImageIndex];
    }

    public function closePhotoModal()
    {
        $this->activeImagePath = null;
        Flux::modal('photo-modal')->close();
    }
};
?>

<!-- Photo Modal -->
<flux:modal name="photo-modal" class="md:max-w-4xl">
    @if ($this->activeImagePath)
        <div class="relative">
            <!-- Navigation Buttons -->
            @if (count($this->allPhotos) > 1)
                <div class="absolute inset-y-0 left-0 flex items-center z-10">
                    <button wire:click="navigatePhoto('prev')"
                        class="ml-2 p-2 rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-75 transition hover:cursor-pointer hover:scale-110"
                        aria-label="Previous photo">
                        <flux:icon icon="chevron-left" class="w-6 h-6" />
                    </button>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center z-10">
                    <button wire:click="navigatePhoto('next')"
                        class="mr-2 p-2 rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-75 transition hover:cursor-pointer hover:scale-110"
                        aria-label="Next photo">
                        <flux:icon icon="chevron-right" class="w-6 h-6" />
                    </button>
                </div>
            @endif

            <!-- Close Button -->
            <div class="absolute top-0 right-0 z-10">
                <button wire:click="closePhotoModal"
                    class="m-2 p-2 rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-75 transition hover:scale-110 hover:cursor-pointer"
                    aria-label="Close">
                    <flux:icon icon="x-mark" class="w-6 h-6" />
                </button>
            </div>

            <!-- Image -->
            <div class="bg-black flex items-center justify-center max-h-[80vh]">
                <img src="{{ asset('storage/' . $this->activeImagePath) }}" alt="Photo {{ $this->activeImageIndex }}"
                    class="max-w-full max-h-[80vh] object-contain">
            </div>

            <!-- Image Counter -->
            @if (count($this->allPhotos) > 1)
                <div class="absolute bottom-0 left-0 right-0 flex justify-center pb-4">
                    <div class="bg-black bg-opacity-75 text-white px-4 py-2 rounded-full text-sm">
                        {{ $this->activeImageIndex }} /
                        {{ count($this->allPhotos) }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</flux:modal>
