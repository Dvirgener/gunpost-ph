<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\tickets\Ticket;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    public $ticket;
    public $status;
    public $priority;

    #[On('getTicket')]
    public function getTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->status = $ticket->status;
        $this->priority = $ticket->priority;
    }

    public function updatedStatus($value)
    {
        if ($this->ticket) {
            $this->ticket->update(['status' => $value]);
        }
        $this->dispatch('refreshList');
    }

    public function updatedPriority($value)
    {
        if ($this->ticket) {
            $this->ticket->update(['priority' => $value]);
        }
        $this->dispatch('refreshList');
    }

    public $updateMessage = '';

    public function addTicketUpdate()
    {
        $this->validate([
            'updateMessage' => 'required|string',
        ]);

        $this->ticket->ticketMessages()->create([
            'user_id' => auth()->id(),
            'ticket_id' => $this->ticket->id,
            'message' => $this->updateMessage,
        ]);

        $this->updateMessage = '';

        // Refresh the ticket data
        $this->ticket = Ticket::find($this->ticket->id);
    }
};
?>

<div>
    @if ($ticket)

        <flux:tab.group class="w-80">
            <flux:tabs variant="segmented" class="w-full">
                <flux:tab name="details">Details</flux:tab>
                <flux:tab name="updates">Updates</flux:tab>

            </flux:tabs>

            <flux:tab.panel name="details" class="w-full">
                <div class="flex flex-col gap-2">
                    <div class="flex gap-3 items-center justify-between">
                        <div class="flex items-center justify-start gap-5 w-3/4 mb-3">
                            <div class=" font-semibold text-xs">created by:</div>
                            {{-- <a href=" {{ route('other.profile', ['user' => $ticket->user->uuid]) }}" class="">
                                <div class="flex items-center gap-2 hover:font-bold">
                                    <flux:tooltip content="{{ $ticket->user->fullName() }}">
                                        <flux:avatar size="sm" src="{{ url('storage/' . $ticket->user->picture) }}"
                                            :name="$ticket->user->first_name" />
                                    </flux:tooltip>
                                </div>
                            </a> --}}
                            <div class="flex items-center gap-2 hover:font-bold">
                                <flux:tooltip content="{{ $ticket->user->first_name }}">
                                    <flux:avatar size="sm" src="{{ url('storage/' . $ticket->user->avatar_path) }}"
                                        :name="$ticket->user->first_name" />
                                </flux:tooltip>
                            </div>
                        </div>



                    </div>
                    <div class="flex gap-3">
                        <div class="flex gap-2 items-center">
                            <div class=" font-semibold text-xs">Status:</div>
                            <flux:text>
                                @switch($ticket->status)
                                    @case('open')
                                        <flux:badge color="green" class="mr-2">Open</flux:badge>
                                    @break

                                    @case('in_progress')
                                        <flux:badge color="amber" class="mr-2">In Progress</flux:badge>
                                    @break

                                    @case('resolved')
                                        <flux:badge color="blue" class="mr-2">Resolved</flux:badge>
                                    @break

                                    @default
                                @endswitch
                            </flux:text>
                        </div>
                        <div class="flex gap-2 items-center">
                            <div class=" font-semibold text-xs">Priority:</div>
                            <flux:text>
                                @switch($ticket->priority)
                                    @case('normal')
                                        <flux:badge color="gray" class="mr-2">Normal</flux:badge>
                                    @break

                                    @case('high')
                                        <flux:badge color="yellow" class="mr-2">High</flux:badge>
                                    @break

                                    @case('urgent')
                                        <flux:badge color="red" class="mr-2">Urgent</flux:badge>
                                    @break

                                    @default
                                @endswitch
                            </flux:text>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <flux:input label="Category" disabled value="{{ $ticket->category }}" />
                        <div>

                        </div>
                    </div>
                    <flux:input label="Subject" disabled value="{{ $ticket->subject }}" />
                    <flux:textarea label="Message" disabled rows="5">{{ $ticket->message }}</flux:textarea>
                </div>
                <div>
                    <flux:heading size="md" class="font-bold mt-4 mb-2">Attachments</flux:heading>
                    @if ($ticket->ticketAttachments)
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ($ticket->ticketAttachments as $photo)
                                <img src="{{ url('storage/' . $photo->file_path) }}" alt=""
                                    class="h-20 w-20 border" />
                            @endforeach
                        </div>
                    @endif
                </div>

            </flux:tab.panel>
            <flux:tab.panel name="updates" class="w-full">
                <div>
                    <div class="mb-2">
                        <flux:select wire:model.live="status" placeholder="Update Status">
                            <flux:select.option value="open">Open</flux:select.option>
                            <flux:select.option value="in_progress">In Progress</flux:select.option>
                            <flux:select.option value="resolved">Resolved</flux:select.option>
                        </flux:select>
                    </div>
                    @if ($ticket->status != 'resolved')
                        <div class="mb-3">
                            <flux:select wire:model.live="priority" placeholder="Update Priority">
                                <flux:select.option value="normal">Normal</flux:select.option>
                                <flux:select.option value="high">High</flux:select.option>
                                <flux:select.option value="urgent">Urgent</flux:select.option>
                            </flux:select>
                        </div>
                    @endif
                    <div class="mb-3">
                        @if ($ticket->status != 'resolved')
                            <form action="" wire:submit="addTicketUpdate">
                                <flux:textarea label="Add Update Message" wire:model="updateMessage" rows="3" />
                                <flux:button type="submit" class="mt-2">Update Ticket</flux:button>
                            </form>
                        @endif

                    </div>
                    <div class="h-120 overflow-y-scroll pe-2">
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
                                    <flux:textarea label="" disabled>
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
            </flux:tab.panel>
        </flux:tab.group>

    @endif
</div>
