<?php

use Livewire\Component;

use App\Models\tickets\Ticket;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component
{
    use WithPagination;

    public $ticketFilter = 'open';

    public $allTicketsCount, $openTicketsCount, $inProgressTicketsCount, $resolvedTicketsCount;


    #[On('refreshList')]
    public function mount()
    {
        $this->allTicketsCount = Ticket::count();
        $this->openTicketsCount = Ticket::where('status', 'open')->count();
        $this->inProgressTicketsCount = Ticket::where('status', 'in_progress')->count();
        $this->resolvedTicketsCount = Ticket::where('status', 'resolved')->count();
    }

    public function updateTicketFilter($filter)
    {
        $this->ticketFilter = $filter;
    }


    public function workOnTicket(Ticket $ticket)
    {
        $ticket->status = 'in_progress';
        $ticket->save();
        $this->tickets();
        $this->dispatch('refreshList');
    }

    public function closeTicket(Ticket $ticket)
    {
        $ticket->status = 'resolved';
        $ticket->save();
        $this->tickets();
        $this->dispatch('refreshList');
    }

    public function openTicket($ticketId)
    {
        $this->dispatch('getTicket', $ticketId);
        Flux::modal('open-ticket')->show();
    }

    #[Computed()]
    public function tickets()
    {
        return Ticket::when($this->ticketFilter != 'all', function ($query) {
            $query->where('status', $this->ticketFilter);
        })->orderByRaw(
                "FIELD(priority, 'urgent', 'high', 'normal')"
            )->paginate(5);
    }
};
