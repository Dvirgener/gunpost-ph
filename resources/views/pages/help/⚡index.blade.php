<?php

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\user\User;
use Flux\Flux;
use Livewire\WithFileUploads;
use App\Models\tickets\Ticket;

new class extends Component {
    use WithFileUploads;

    public $ticket_subject;
    public $ticket_message;
    public $ticket_category = 'bug'; // Default category

    // * Create Tickets
    public function createTicket()
    {
        $this->validate([
            'ticket_subject' => 'required|string|max:255',
            'ticket_message' => 'required|string|min:10|max:2000',
            'ticket_category' => 'required|string', // Adjust categories as needed
            'ticket_photos' => 'array|max:5|nullable', // Limit to 5 photos
            'ticket_photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate each photo
        ]);

        $ticket = new Ticket();
        $ticket->user_id = auth()->user()->id;
        $ticket->subject = $this->ticket_subject;
        $ticket->message = $this->ticket_message;
        $ticket->category = $this->ticket_category;
        $ticket->status = 'open'; // Default status
        $ticket->priority = 'normal'; // Default priority
        $ticket->save();

        // Handle file uploads
        if (!empty($this->ticket_photos)) {
            foreach ($this->ticket_photos as $photo) {
                $path = $photo->store('ticket_photos', 'public'); // Store in public disk
                $ticket->ticketAttachments()->create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                ]);
            }
        }

        // Reset the form fields
        $this->reset(['ticket_subject', 'ticket_message', 'ticket_category', 'ticket_photos']);

        $this->activeTickets = Ticket::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Flash a success message to the session
        Flux::toast(heading: 'Ticket Created!', text: 'Your ticket has been created successfully. An admin will get back to you shortly.', variant: 'success');
    }
    // * Create Tickets

    // * Photos for the ticket
    public $ticket_photos = [];

    public function removePhoto($index)
    {
        $photo = $this->ticket_photos[$index];
        $photo->delete();
        unset($this->ticket_photos[$index]);
        $this->ticket_photos = array_values($this->ticket_photos);
    }
    // * Photos for the ticket

    public $admins;
    public $activeTickets;
    public function mount()
    {
        if (!auth()->guest()) {
            $this->admins = User::where('account_type', '=', 'TFT_admin')->pluck('id');
            $this->activeTickets = Ticket::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }
};
?>

<div class="flex flex-col h-full">

    <div class="flex flex-col h-full">
        <div class="shrink-0 mb-2">
            <x-virg.guest-callout />

            <div class="flex items-center gap-3 mb-6">
                <flux:icon.question-mark-circle class="w-8 h-8 text-purple-600" />
                <h1 class="font-bold text-2xl">HELP CENTER</h1>
            </div>
            <flux:text class="mt-2 text-center md:text-start text-gray-700 dark:text-gray-300">Welcome to the GunPost PH
                Help
                Center! This page will guide you through how to organize your inventory and post items online
                efficiently.
            </flux:text>
        </div>

        <div class="flex-1 min-h-0 overflow-y-scroll px-3">

            <!-- Getting Started Guide -->
            <flux:separator class="my-6" />

            <!-- Site Navigation Section -->
            <div
                class="my-6 p-5  from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/10 border-l-4 border-purple-600 rounded-r-lg">
                <flux:heading size="lg" class="text-purple-700 dark:text-purple-300 mb-4 flex items-center gap-2">
                    <flux:icon.map-pin class="w-5 h-5" />
                    How to Navigate the Site
                </flux:heading>
                <flux:text class="text-center md:text-start mb-3 text-gray-700 dark:text-gray-300">
                    GunPost PH is designed to be user-friendly and intuitive. Here's how to get around:
                </flux:text>
                <ul class="space-y-3 ms-4">
                    <li class="text-sm text-gray-700 dark:text-gray-300"><span
                            class="inline-block w-2 h-2 bg-purple-600 rounded-full mr-2"></span><strong
                            class="text-purple-700 dark:text-purple-300">Sidebar Navigation:</strong> Use the sidebar
                        menu
                        on the left to access different sections including Dashboard, Browse Posts, Messages, Tickets,
                        and
                        more.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><span
                            class="inline-block w-2 h-2 bg-purple-600 rounded-full mr-2"></span><strong
                            class="text-purple-700 dark:text-purple-300">Profile:</strong> Your Profile where you can
                        view your posts, messages, and account overview.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><span
                            class="inline-block w-2 h-2 bg-purple-600 rounded-full mr-2"></span><strong
                            class="text-purple-700 dark:text-purple-300">Browse Posts:</strong> Search and filter
                        through
                        available posts from our community members.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><span
                            class="inline-block w-2 h-2 bg-purple-600 rounded-full mr-2"></span><strong
                            class="text-purple-700 dark:text-purple-300">Messages:</strong> Communicate with other users
                        about their posts or inquiries.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><span
                            class="inline-block w-2 h-2 bg-purple-600 rounded-full mr-2"></span><strong
                            class="text-purple-700 dark:text-purple-300">Account Settings:</strong> Manage your profile,
                        preferences, and account information.</li>
                </ul>
            </div>

            <!-- Creating Posts Section -->
            <div
                class="my-6 p-5 from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/10 border-l-4 border-purple-600 rounded-r-lg">
                <flux:heading size="lg" class="text-purple-700 dark:text-purple-300 mb-4 flex items-center gap-2">
                    <flux:icon.document-plus class="w-5 h-5" />
                    How to Create Posts
                </flux:heading>
                <flux:text class="text-center md:text-start mb-3 text-gray-700 dark:text-gray-300">
                    Creating and publishing posts on GunPost PH is simple. Follow these steps:
                </flux:text>
                <ol class="space-y-3 ms-4">
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">1. Navigate to Create Post:</strong> Look for
                        the
                        "Create Post" or "New Post" button in your profile or in the Posts page.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">2. Fill in Details:</strong>Select the Category
                        and
                        Enter the post title,
                        description, and any other relevant information about your item.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">3. Add Photos:</strong> Upload clear photos of
                        the
                        item from different angles to attract potential buyers/sellers.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">4. Set Price & Terms:</strong> Specify the
                        price,
                        condition, and any terms of sale.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">5. Review & Submit:</strong> Double-check all
                        information and send to admin for review and approval.</li>
                    <li class="text-sm text-gray-700 dark:text-gray-300"><strong
                            class="text-purple-700 dark:text-purple-300">6. Publication:</strong> After an admin
                        approves
                        your post, it will be published on the platform for a period of 2 months.</li>
                </ol>
            </div>

            <!-- Post Credits Section - Important -->
            <flux:callout color="purple" class="my-6 dark:bg-purple-900/30">
                <x-slot name="icon">
                    <flux:icon.star />
                </x-slot>
                <flux:callout.heading>Important: Purchasing Post Credits</flux:callout.heading>
                <flux:callout.text>
                    <p class="mb-3 text-gray-700 dark:text-gray-300">
                        To publish posts on GunPost PH, you must purchase post credits. Here's what you need to know:
                    </p>
                    <ul class="space-y-2 ms-4">
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">Credits Required:</strong> Each published
                            post
                            requires post credits.
                        </li>
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">Flexible Packages:</strong> We offer
                            various
                            credit packages to suit your needs, from individual posts to bulk publishing options.</li>
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">Easy Purchase:</strong> You can purchase
                            post
                            credits directly from the Orders page. Multiple payment methods are also available.</li>
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">No Expiration for Unused Credits:</strong>
                            Purchased credits remain in your account until you use them.</li>
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">How to Purchase:</strong> Go to the Orders
                            page, Select a credit package and place the order. After which, an admin will contact you
                            for
                            the payment and after payment is received, your credits will be activated.</li>
                        <li class="text-sm text-gray-700 dark:text-gray-300">✓ <strong
                                class="text-purple-700 dark:text-purple-300">Post Duration:</strong> Each Post credit
                            allows
                            your post to be published for a period of 2 months. To republish expired posts, you can
                            republish it on your profile page in one click. It will automatically renew as long as you
                            have
                            sufficient credits.</li>
                    </ul>
                </flux:callout.text>
            </flux:callout>
            <flux:separator class="my-6" />

            @auth
                <div class="my-5">
                    <div class="p-5   shadow-md">
                        <flux:heading size="lg" class="mb-2 flex items-center gap-2 justify-center md:justify-start">
                            <flux:icon.exclamation-triangle class="w-5 h-5" />
                            "If something doesn't look right, we're here to make it right."
                        </flux:heading>
                        <flux:text class="">Contact Admin Support — Suggestions, bugs, errors, user Reports or
                            system issues? Let us know. Create a Ticket and we'll resolve it for you.</flux:text>
                    </div>
                    <div class="my-4">
                        <div>
                            <form action="" wire:submit="createTicket" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-2">
                                    <flux:select label="Category" wire:model="ticket_category"
                                        placeholder="Choose Ticket Category...">
                                        <flux:select.option>Bug</flux:select.option>
                                        <flux:select.option>Error</flux:select.option>
                                        <flux:select.option>Suggestion</flux:select.option>
                                        <flux:select.option>Comment</flux:select.option>
                                        <flux:select.option>Inquiry</flux:select.option>
                                        <flux:select.option>Report User</flux:select.option>
                                        <flux:select.option>Other</flux:select.option>
                                    </flux:select>
                                </div>
                                <div class="mb-2">
                                    <flux:input label="Subject" wire:model="ticket_subject"
                                        placeholder="Subject of the Ticket" />
                                </div>
                                <div class="mb-2">
                                    <flux:textarea label="Message" placeholder="Type your Message here..."
                                        wire:model="ticket_message" />
                                </div>
                                <div class="mb-2">
                                    <flux:file-upload wire:model="ticket_photos" multiple label="Upload files">
                                        <flux:file-upload.dropzone heading="Drop files or click to browse"
                                            text="JPG, PNG, GIF up to 10MB" inline />
                                    </flux:file-upload>
                                </div>
                                <div class="">
                                    <flux:button type="submit" class="mt-2 hover:cursor-pointer" variant="primary"
                                        icon="ticket">
                                        Create
                                        Ticket
                                    </flux:button>
                                </div>
                                <div class="mt-4 flex flex-col gap-2">
                                    @if ($ticket_photos)
                                        @foreach ($ticket_photos as $photo)
                                            <flux:file-item :heading="$photo->getClientOriginalName()"
                                                :image="$photo->temporaryUrl()" :size="$photo->getSize()">
                                                <x-slot name="actions">
                                                    <flux:file-item.remove
                                                        wire:click="removePhoto('{{ $loop->index }}')" />
                                                </x-slot>
                                            </flux:file-item>
                                        @endforeach
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="mt-10">
                        <flux:heading size="md"
                            class="font-bold text-center md:text-start mb-4 text-purple-700 dark:text-purple-300 flex items-center gap-2 justify-center md:justify-start">
                            <flux:icon.ticket class="w-5 h-5" />
                            Your Tickets
                        </flux:heading>
                        @if ($activeTickets->isEmpty())
                            <flux:text class="text-center md:text-start">You have no tickets made.</flux:text>
                        @else
                            <div
                                class="space-y-2 md:mx-0 mb-4 overflow-y-scroll h-96 pe-2 border-l-4 border-purple-600 ps-3">
                                <flux:accordion transition exclusive variant="reverse">
                                    @foreach ($activeTickets as $ticket)
                                        <flux:accordion.item>
                                            <flux:accordion.heading>
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex gap-2">
                                                        @switch($ticket->status)
                                                            @case('open')
                                                                <flux:badge size="sm" color="green">Open</flux:badge>
                                                            @break

                                                            @case('in_progress')
                                                                <flux:badge size="sm" color="orange">In Progress</flux:badge>
                                                            @break

                                                            @case('resolved')
                                                                <flux:badge size="sm" color="blue">Resolved</flux:badge>
                                                            @break
                                                        @endswitch
                                                    </div>
                                                    <p>{{ $ticket->subject }}</p>
                                                </div>
                                            </flux:accordion.heading>
                                            <flux:accordion.content>
                                                <div class="ps-7">
                                                    <div class="mb-2">
                                                        <span class="font-bold text-xs">Category: </span>
                                                        <flux:badge size="sm">{{ $ticket->category }}</flux:badge>
                                                    </div>

                                                    <p class="mb-2"><span class="font-bold text-xs">Content:</span>
                                                        {{ $ticket->message }}</p>

                                                    <div space-y-2>
                                                        <div class="mb-2">
                                                            <span class="font-bold text-xs">Attachment/s: </span>
                                                        </div>
                                                        <div class="flex gap-3 mb-2">
                                                            @foreach ($ticket->ticketAttachments as $photo)
                                                                <img src="{{ url('storage/' . $photo->file_path) }}"
                                                                    alt="" class="h-20 w-20 border" />
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-xs">Action Taken :</span>
                                                    </div>
                                                    <div>
                                                        @forelse ($ticket->ticketMessages as $update)
                                                            <div class="flex items-center gap-2 mb-3 border-b pb-2">
                                                                <div
                                                                    class="flex items-center gap-2 hover:font-bold border">
                                                                    <flux:tooltip
                                                                        content="{{ $update->user->first_name }}">
                                                                        <flux:avatar size="sm"
                                                                            src="{{ $update->user->avatar_path ? url('storage/' . $update->user->avatar_path) : asset('blank_image.png') }}"
                                                                            :name="$update->user->first_name" />
                                                                    </flux:tooltip>
                                                                </div>
                                                                <div class="w-full">
                                                                    <div class="text-xs text-gray-500 ">
                                                                        {{ $update->created_at?->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}
                                                                    </div>
                                                                    <flux:textarea label="" disabled rows="3">
                                                                        {{ $update->message }}
                                                                    </flux:textarea>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-center text-gray-500 mt-4">
                                                                No updates have been added yet.
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </flux:accordion.content>
                                        </flux:accordion.item>
                                    @endforeach
                                </flux:accordion>
                            </div>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
