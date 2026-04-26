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

    public $allPostCount;
    public $allPublishedPostCount;
    public $allExpiredPostCount;
    public $rejectedPostCount;
    public $allArchivedPostCount;

    public $totalAmountOfOrdersPlaced;
    public $totalRevenue;

    public function mount(){
        $this->userCount = User::all()->count();
        $this->pendingUserCount = User::whereHas('verification', function($query){
            $query->where('kyc_status', '=', 'pending');
        })->count();

        $this->allPostCount = Post::all()->count();
        $this->allPublishedPostCount = Post::where('status', '=', 'approved')->count();
        $this->postCount = Post::all()->count();
        $this->pendingPostCount = Post::where('status', '=', 'pending')->count();
        $this->rejectedPostCount = Post::where('status', '=', 'rejected')->count();
        $this->allExpiredPostCount = Post::where('status', '=', 'expired')->count();
        $this->allArchivedPostCount = Post::where('status', '=', 'archived')->count();

        $this->orderCount = Order::all()->count();
        $this->pendingOrderCount = Order::where('status', '=', 'pending')->count();

        $this->ticketCount = Ticket::all()->count();
        $this->pendingTicketCount = Ticket::where('status', '=', 'pending')->count();

        $this->totalAmountOfOrdersPlaced = Order::all()->sum('amount');
        $this->totalRevenue = Order::where('status', '=', 'paid')->sum('amount');
    }

};
