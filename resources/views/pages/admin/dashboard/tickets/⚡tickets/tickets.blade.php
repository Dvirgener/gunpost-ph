<div>
    <div class="flex justify-between">
        <flux:heading size="lg" class="font-mono text-start my-2 font-bold">TICKETS</flux:heading>
        <flux:button color="red" variant="primary" class="hover:cursor-pointer" href="{{ route('dashboard') }}">
            Back
        </flux:button>
    </div>
    <div class="w-full">
        <div class="grid grid-cols-4 gap-4 my-4 w-full">
            <button wire:click="updateTicketFilter('all')"
                class="border rounded-md {{ $ticketFilter == 'all' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="All Tickets" numbers="{{ $allTicketsCount }}" />
            </button>
            <button wire:click="updateTicketFilter('open')"
                class="border rounded-md {{ $ticketFilter == 'open' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Open" numbers="{{ $openTicketsCount }}" />
            </button>
            <button wire:click="updateTicketFilter('in_progress')"
                class="border rounded-md {{ $ticketFilter == 'in_progress' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="In Progress" numbers="{{ $inProgressTicketsCount }}" />
            </button>
            <button wire:click="updateTicketFilter('resolved')"
                class="border rounded-md {{ $ticketFilter == 'resolved' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Resolved" numbers="{{ $resolvedTicketsCount }}" />
            </button>
        </div>
        <div>
            <div>
                <flux:pagination :paginator="$this->tickets" />
            </div>
        </div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Priority</flux:table.column>
                <flux:table.column>Date Created</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->tickets as $ticket)
                    <flux:table.row>

                        <flux:table.cell>
                            {{-- <a href=" {{ route('other.profile', ['user' => $ticket->user->uuid]) }}" class="">
                                <div class="flex items-center gap-2 hover:font-bold">
                                    <flux:tooltip content="{{ $ticket->user->fullName() }}">
                                        <flux:avatar size="sm"
                                            src="{{ url('storage/' . $ticket->user->picture) }}"
                                            :name="$ticket->user->first_name" />
                                    </flux:tooltip>
                                </div>
                            </a> --}}
                            <div class="flex items-center gap-2 hover:font-bold">
                                <flux:tooltip content="{{ $ticket->user->first_name }}">
                                    <flux:avatar size="sm"
                                        src="{{ url('storage/' . $ticket->user->avatar_path) }}"
                                        :name="$ticket->user->first_name" />
                                </flux:tooltip>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell wire:click="openTicket('{{ $ticket->id }}')"
                            class="hover:cursor-pointer hover:font-bold">
                            {{ $ticket->subject }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:text>{{ $ticket->category }}</flux:text>
                        </flux:table.cell>

                        <flux:table.cell class="gap-2">
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
                        </flux:table.cell>

                        <flux:table.cell class="gap-2">
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
                        </flux:table.cell>


                        <flux:table.cell>
                            {{ $ticket->created_at->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}
                        </flux:table.cell>


                        <flux:table.cell>
                            @if ($ticket->status == 'open')
                                <flux:tooltip content="Work on Ticket">
                                    <flux:button square>
                                        <flux:icon.check class="hover:cursor-pointer hover:text-amber-600"
                                            wire:click="workOnTicket('{{ $ticket->id }}')" />
                                    </flux:button>
                                </flux:tooltip>
                            @elseif($ticket->status == 'in_progress')
                                <flux:tooltip content="Close Ticket">
                                    <flux:button square>
                                        <flux:icon.x-mark class="hover:cursor-pointer hover:text-amber-600"
                                            wire:click="closeTicket('{{ $ticket->id }}')" />
                                    </flux:button>
                                </flux:tooltip>
                            @endif

                        </flux:table.cell>


                    </flux:table.row>
                @endforeach

            </flux:table.rows>
        </flux:table>
    </div>
    <flux:modal name="open-ticket" variant="flyout">
        <livewire:pages::admin.dashboard.tickets.details />
    </flux:modal>
</div>
