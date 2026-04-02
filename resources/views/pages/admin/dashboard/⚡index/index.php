<?php

use Livewire\Component;
use App\Models\user\User;
use App\Models\posts\Post;
use App\Models\Order;
use App\Models\tickets\Ticket;


new class extends Component
{
    public $userCount, $pendingUserCount;
    public $postCount, $pendingPostCount;
    public $orderCount, $pendingOrderCount;
    public $ticketCount, $pendingTicketCount;

    public function mount(){
        $this->userCount = User::all()->count();
        $this->pendingUserCount = User::where('status', '=', 'pending')->count();

        $this->postCount = Post::all()->count();
        $this->pendingPostCount = Post::where('status', '=', 'pending')->count();

        $this->orderCount = Order::all()->count();
        $this->pendingOrderCount = Order::where('status', '=', 'pending')->count();

        $this->ticketCount = Ticket::all()->count();
        $this->pendingTicketCount = Ticket::where('status', '=', 'pending')->count();
    }

};
