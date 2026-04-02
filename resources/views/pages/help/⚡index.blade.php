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
        $this->admins = User::where('account_type', '=', 'TFT_admin')->pluck('id');
        $this->activeTickets = Ticket::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
};
?>

<div class="p-4">

    <h1 class="font-bold text-2xl">HELP</h1>
    <flux:text class="mt-2 text-center md:text-start">Welcome to the GunPost PH Help Center!
        This page will guide you through how to organize your inventory and post items online efficiently.
    </flux:text>
    <div>
        @auth
            <div class="my-5">

                <flux:heading size="lg" class="text-center md:text-start">“If something doesn’t look right, we’re here to
                    make
                    it right.”
                </flux:heading>

                <flux:text class="text-center md:text-start">Contact Admin Support — Suggestions, bugs, errors, user Reports
                    or
                    system
                    issues? Let us know. Create a Ticket and we'll resolve it for you.</flux:text>

                <div class="my-4 w-full">
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
                                                <flux:file-item.remove wire:click="removePhoto('{{ $loop->index }}')" />
                                            </x-slot>
                                        </flux:file-item>
                                    @endforeach
                                @endif
                            </div>


                        </form>
                    </div>

                </div>
                <div>
                    <flux:heading size="md" class="font-bold text-center md:text-start mb-2">Your Tickets:
                    </flux:heading>
                    @if ($activeTickets->isEmpty())
                        <flux:text class="text-center md:text-start">You have no tickets made.</flux:text>
                    @else
                        <div class="space-y-2 md:mx-0 mb-4 overflow-y-scroll h-96 pe-2">
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
                                                            <div class="flex items-center gap-2 hover:font-bold border">
                                                                <flux:tooltip content="{{ $update->user->first_name }}">
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
