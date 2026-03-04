<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Gun;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Flux\Flux;

new class extends Component {
    #[Locked]
    public Post $post;

    public ?string $activeImagePath = null;
    public ?string $activeImageIndex = null;

    #[Computed]
    public function gun()
    {
        return $this->post->gun;
    }

    #[Computed]
    public function allPhotos(): array
    {
        $photos = [];
        for ($i = 1; $i <= 10; $i++) {
            $photo = $this->post->{"p_$i"};
            if ($photo) {
                $photos[$i] = $photo;
            }
        }
        return $photos;
    }

    public function navigatePhoto(string $direction): void
    {
        if (!$this->activeImageIndex) {
            return;
        }

        $currentIndex = (int) $this->activeImageIndex;
        $photos = $this->allPhotos;

        $photoIndices = array_keys($photos);

        $currentPosition = array_search($currentIndex, $photoIndices);

        if ($currentPosition === false) {
            return;
        }

        if ($direction === 'next') {
            $nextPosition = $currentPosition + 1;
            if ($nextPosition < count($photoIndices)) {
                $nextIndex = $photoIndices[$nextPosition];
                $this->openPhotoModal($nextIndex);
            }
        } else {
            $prevPosition = $currentPosition - 1;
            if ($prevPosition >= 0) {
                $prevIndex = $photoIndices[$prevPosition];
                $this->openPhotoModal($prevIndex);
            }
        }
    }

    public function closePhotoModal(): void
    {
        $this->activeImagePath = null;
        $this->activeImageIndex = null;
        flux::modal('photo-modal')->close();
    }
};
?>

<div class="min-h-screen py-8">
    {{-- <!-- Back Button & Title -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <flux:button href="{{ route('posts') }}" variant="ghost">← Back to Posts</flux:button>
        <h1 class="text-4xl font-bold mt-4 mb-2">{{ $this->post->title }}</h1>
        <p class="text-gray-600">
            <flux:badge :color="$this->post->listing_type == 'sell' ? 'green' : 'violet'">
                {{ ucfirst($this->post->listing_type) }}</flux:badge> - Posted
            {{ $this->post->created_at->diffForHumans() }}
        </p>
    </div> --}}



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
                    <img src="{{ asset('storage/' . $this->activeImagePath) }}"
                        alt="Photo {{ $this->activeImageIndex }}" class="max-w-full max-h-[80vh] object-contain">
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
</div>
