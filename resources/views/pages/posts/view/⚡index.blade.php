<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Gun;
use App\Models\posts\categories\Ammunition;
use App\Models\posts\categories\Airsoft;
use App\Models\posts\categories\Accessory;
use App\Models\posts\categories\Other;
use Livewire\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Flux\Flux;
use Livewire\Attributes\Rule;
use App\Models\Conversation;
use App\Models\Message;

new class extends Component {
    #[Locked]
    public Post $post;

    public string $category;

    #[Locked]
    public Gun $gun;

    #[Locked]
    public ?Ammunition $ammunition = null;

    #[Locked]
    public ?Airsoft $airsoft = null;

    #[Locked]
    public ?Accessory $accessory = null;

    #[Locked]
    public ?Other $others = null;

    public $photos;

    #[Computed]
    public function mainPhoto(): ?string
    {
        return $this->post->p_1;
    }

    #[Computed]
    public function thumbnails(): array
    {
        $thumbnails = [];
        for ($i = 2; $i <= 10; $i++) {
            $photo = $this->post->{"p_$i"};
            if ($photo) {
                $thumbnails[$i] = $photo;
            }
        }
        return $thumbnails;
    }

    public function mount(Post $post, $category)
    {
        $this->post = $post;
        $this->category = $category;
        switch ($category) {
            case 'gun':
                $this->gun = $post->gun;
                break;
            case 'ammunition':
                $this->ammunition = $post->ammunition;
                break;
            case 'airsoft':
                $this->airsoft = $post->airsoft;
                break;
            case 'accessory':
                $this->accessory = $post->accessory;
                break;
            case 'others':
                $this->others = $post->others;
                break;
            // Add cases for other categories as needed
        }
        $this->photos = $this->allPhotos();

        $this->checkViewer();
    }

    private function checkViewer()
    {
        $identifier = auth()->check() ? auth()->id() : request()->ip();
        // Increment view count
        $alreadyViewed = \DB::table('views')->where('post_id', $this->post->id)->where('viewer_ip', $identifier)->exists();

        if (!$alreadyViewed) {
            // Log the page view
            \DB::table('views')->insert([
                'post_id' => $this->post->id,
                'viewer_ip' => $identifier,
                'viewer_user_agent' => request()->userAgent(),
            ]);
            $this->post->increment('views');
        }
    }

    // THIS PART IS ALL ABOUT PHOTO MODAL

    public ?string $activeImagePath = null;
    public ?string $activeImageIndex = null;

    public function openPhotoModal(int $index): void
    {
        // $this->activeImageIndex = (string) $index;
        // $this->activeImagePath = $this->post->{"p_$index"};

        $this->dispatch('openPhotoModalEvent', ['index' => $index]);
        Flux::modal('photo-modal')->show();
    }

    private function allPhotos(): array
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

    public function deletePost()
    {
        // $folder = "posts/{$this->post->uuid}";

        // if (Storage::disk('public')->exists($folder)) {
        //     Storage::disk('public')->deleteDirectory($folder);
        // }

        // switch ($this->category) {
        //     case 'gun':
        //         $this->post->gun()->delete();
        //         break;
        //     case 'ammunition':
        //         $this->post->ammunition()->delete();
        //         break;
        // }
        $this->post->delete();

        return redirect(route('posts'))->with('success', 'Post deleted successfully.');
    }

    // METHOD FOR SENDING INQUIRY MESSAGE TO POST OWNER

    #[Rule(['required'])]
    public string $inquiryMessage = '';

    private function message(Conversation $convo)
    {
        Message::create([
            'conversation_id' => $convo->id,
            'sender_id' => auth()->user()->id,
            'body' => $this->inquiryMessage,
            'read_by' => [auth()->user()->id],
        ]);
    }

    public function sendInquiry()
    {
        $this->validate();

        if ($this->inquiryMessage === '') {
            flux::toast('Message cannot be empty.', variant: 'danger');
            return;
        }

        $convo = Conversation::where('type', 'post')
            ->where('post_id', $this->post->id)
            ->where('initiator_id', auth()->user()->id)
            ->first();

        if ($convo) {
            $this->message($convo);
            flux::toast('Message sent successfully.', variant: 'success');
            Flux::modal('sendInquiry')->close();
            return;
        } else {
            $con = Conversation::create([
                'type' => 'post',
                'post_id' => $this->post->id,
                'initiator_id' => auth()->user()->id,
            ]);
            $con->participants()->sync([$this->post->user->id, auth()->user()->id]);

            Message::create([
                'conversation_id' => $con->id,
                'sender_id' => auth()->user()->id,
                'body' => $this->inquiryMessage,
                'read_by' => [auth()->user()->id],
            ]);
            flux::toast('Message sent successfully.', variant: 'success');
            Flux::modal('sendInquiry')->close();
            return;
        }
    }
};
?>

<div class="flex flex-col h-full py-8">

    <x-virg.guest-callout />


    <!-- Back Button & Title -->
    <div class="flex-1 shrink-0 w-full mx-auto mb-6 ">
        <flux:button href="{{ route('posts') }}" variant="ghost">← Back to Posts</flux:button>
        <div class="flex items-center justify-between">
            <h1 class="text-4xl font-bold mt-4 mb-2">{{ $this->post->title }}</h1>
            <div class="flex gap-2 ">
                @auth
                    @if (auth()->user()->id === $this->post->user_id)
                        @switch($category)
                            @case('gun')
                                <flux:button variant="primary" color="blue" icon="pencil"
                                    href="{{ route('posts.edit.category.gun', $this->post->uuid) }}">Edit</flux:button>
                            @break

                            @case('ammunition')
                                <flux:button variant="primary" color="blue" icon="pencil"
                                    href="{{ route('posts.edit.category.ammunition', $this->post->uuid) }}">Edit</flux:button>
                            @break

                            @case('airsoft')
                                <flux:button variant="primary" color="blue" icon="pencil"
                                    href="{{ route('posts.edit.category.airsoft', $this->post->uuid) }}">Edit</flux:button>
                            @break

                            @case('accessory')
                                <flux:button variant="primary" color="blue" icon="pencil"
                                    href="{{ route('posts.edit.category.accessory', $this->post->uuid) }}">Edit</flux:button>
                            @break

                            @case('others')
                                <flux:button variant="primary" color="blue" icon="pencil"
                                    href="{{ route('posts.edit.category.others', $this->post->uuid) }}">Edit</flux:button>
                            @break
                        @endswitch

                        <flux:button variant="primary" color="red" icon="trash" wire:click="deletePost"
                            wire:confirm="Are you sure you want to delete this post?">
                            Delete
                        </flux:button>
                    @else
                        <flux:modal.trigger name="sendInquiry">
                            <flux:button variant="outline" color="red" icon="chat-bubble-left">Send Inquiry</flux:button>
                        </flux:modal.trigger>
                        {{-- <flux:modal.trigger name="reportPost">
                            <flux:button variant="primary" color="red" icon="flag">Report</flux:button>
                        </flux:modal.trigger> --}}
                    @endif
                @endauth
            </div>
        </div>

        <p class="text-gray-600">
            <flux:badge :color="$this->post->listing_type == 'sell' ? 'green' : 'violet'">
                {{ ucfirst($this->post->listing_type) }}</flux:badge> - Posted
            {{ $this->post->created_at->diffForHumans() }}
        </p>
    </div>

    <div class="flex flex-col space-y-2 min-h-0 overflow-y-scroll pe-3 pb-5">

        {{-- Photos and Main Info Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Photos Section -->
            <div class="lg:col-span-2">
                <div class="rounded-lg shadow ">
                    <!-- Main Photo -->
                    @if ($this->mainPhoto)
                        <div class="relative aspect-square flex items-center justify-center cursor-pointer group"
                            wire:click="openPhotoModal(1)">
                            {{-- <img src="{{ $this->post->p_1 }}" alt="{{ $this->post->title }}"
                                class="w-full h-full object-cover group-hover:opacity-90 transition-opacity"> --}}
                            <img src="{{ asset('storage/' . $this->mainPhoto) }}" alt="{{ $this->post->title }}"
                                class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                            <div
                                class="absolute inset-0  bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                                <flux:icon icon="magnifying-glass"
                                    class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                            </div>
                        </div>
                    @else
                        <div class="aspect-square bg-gray-200 flex items-center justify-center">
                            <flux:icon icon="photo" class="w-16 h-16 text-gray-400" />
                        </div>
                    @endif

                    <!-- Thumbnails -->
                    @if (count($this->thumbnails) > 0)
                        <div class="grid grid-cols-5 gap-2 p-4 ">
                            @foreach ($this->thumbnails as $index => $photo)
                                <div class="relative aspect-square rounded cursor-pointer group bg-gray-100 overflow-hidden"
                                    wire:click="openPhotoModal({{ $index }})">
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Photo {{ $index }}"
                                        class="w-full h-full object-cover group-hover:opacity-75 transition-opacity">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                </flux:card>
            </div>

            <!-- Info Section -->
            <div class="lg:col-span-1">
                <div
                    class="dark:bg-stone-900 text-gray-700 rounded-lg shadow shadow-black dark:shadow-white/50 p-6 space-y-6">
                    <!-- Price -->
                    <div>
                        <flux:heading level="3" class="mb-2 text-md text-black dark:text-white">Price
                        </flux:heading>
                        @if ($this->post->listing_type === 'sell')
                            <p class="font-bold text-blue-600 text-lg">
                                P{{ number_format($this->post->price, 2) }}
                            </p>
                            @if ($this->post->is_negotiable)
                                <p class="text-sm text-gray-600 dark:text-white mt-2">Negotiable</p>
                            @endif
                        @else
                            <p class="font-bold text-blue-600 text-xl">
                                P{{ number_format($this->post->buy_min_price, 2) }} -
                                P{{ number_format($this->post->buy_max_price, 2) }}
                            </p>
                        @endif
                    </div>

                    <flux:separator />

                    {{-- # INSERT CATEGORIES HERE --}}

                    <!-- Key Details -->
                    @switch($this->category)
                        @case('gun')
                            <livewire:pages::posts.view.category.main.gun :gun="$this->post->gun" />
                        @break

                        @case('ammunition')
                            <livewire:pages::posts.view.category.main.ammunition :ammunition="$this->post->ammunition" />
                        @break

                        @case('airsoft')
                            <livewire:pages::posts.view.category.main.airsoft :airsoft="$this->post->airsoft" />
                        @break

                        @case('accessory')
                            <livewire:pages::posts.view.category.main.accessory :accessory="$this->post->accessory" />
                        @break

                        @case('others')
                            <livewire:pages::posts.view.category.main.others :other="$this->post->other" />
                        @break

                        @default
                    @endswitch

                    @if ($this->post->post_condition)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-white">Listing Type:</span>
                            <span
                                class="font-medium dark:text-white/50">{{ ucfirst(str_replace('_', ' ', $this->post->post_condition)) }}</span>
                        </div>
                    @endif

                    <flux:separator />

                    <!-- Location -->
                    @if ($this->post->location)
                        <div>
                            <flux:heading level="3" class="mb-2 text-black dark:text-white">Location</flux:heading>
                            <p class="text-gray-700 dark:text-white/50">{{ $this->post->location }}</p>
                        </div>

                        <flux:separator />
                    @endif

                    <!-- Seller Info -->
                    <div>
                        <flux:heading level="3" class="mb-4 text-black dark:text-white">Posted by:</flux:heading>
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="$this->post->user->first_name"
                                src="{{ $this->post->user->avatar_path ? url('storage/' . $this->post->user->avatar_path) : asset('blank_image.png') }}"
                                class="w-10 h-10" />
                            <div>
                                <a href="{{ route('profile.visit', $this->post->user->uuid) }}"
                                    class="hover:underline">
                                    <p class="font-medium dark:text-white/50">{{ $this->post->user->first_name }}</p>
                                </a>

                                <p class="text-sm text-gray-600 dark:text-white/50">{{ $this->post->user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Details Section --}}
        <div class="w-full mb-5  ">

            <!-- Description Section -->
            @if ($this->post->description)
                <div class="mt-8 bg-white dark:bg-stone-900 rounded-lg shadow dark:shadow-white/50 p-6">
                    <flux:heading level="3" class="mb-4 text-black dark:text-white text-xl">Description
                    </flux:heading>
                    <p class="text-gray-700 dark:text-white/50 whitespace-pre-wrap text-sm">
                        {{ $this->post->description }}
                    </p>
                </div>
            @endif


            {{-- # INSERT CATEGORIES HERE --}}
            @auth
                <!-- Key Details -->
                @switch($this->category)
                    @case('ammunition')
                        <livewire:pages::posts.view.category.details.ammunition :ammunition="$this->post->ammunition" />
                    @break

                    @case('gun')
                        <livewire:pages::posts.view.category.details.gun :gun="$this->post->gun" :activeImageIndex="$this->activeImageIndex" />
                    @break

                    @case('airsoft')
                        <livewire:pages::posts.view.category.details.airsoft :airsoft="$this->post->airsoft" />
                    @break

                    @case('accessory')
                        <livewire:pages::posts.view.category.details.accessory :accessory="$this->post->accessory" />
                    @break

                    @case('others')
                        <livewire:pages::posts.view.category.details.others :others="$this->post->other" />
                    @break

                    @default
                @endswitch
            @endauth


        </div>

    </div>






    <livewire:pages::posts.view.photo-modal :photos="$this->photos" />


    {{-- Modal for Sending the User a Message --}}

    <flux:modal name="sendInquiry" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Make Inquiry</flux:heading>
                <flux:text class="mt-2">Send a message to the Post Owner.</flux:text>
            </div>
            <form action="" wire:submit.prevent="sendInquiry" class="space-y-4">
                <flux:textarea label="Message" placeholder="Your message" wire:model="inquiryMessage" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Send Message</flux:button>
                </div>

            </form>

        </div>
    </flux:modal>
</div>
