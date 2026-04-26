<?php

use Livewire\Component;
use App\Models\user\User;
use App\Models\posts\Post;
use App\Models\Order;
use App\Models\tickets\Ticket;

new class extends Component
{
    // POSTS
    public $allPostsCount, $publishedPostsCount, $pendingPostsCount, $rejectedPostsCount, $expiredPostsCount, $archivedPostsCount;

    // USERS
    public $allUsersCount, $kycVerifiedUsersCount, $pendingKycUsersCount, $kycForApproval, $flaggedUsersCount;

    // CREDITS
    public $confirmedCreditOrdersCount, $pendingCreditOrdersCount;

    // HELP
    public $openTicketsCount, $pendingTicketsCount, $closedTicketsCount;


    public function mount(){

    $this->allPostsCount = Post::all()->count();
    $this->publishedPostsCount = Post::where('status', '=', 'approved')->count();
    $this->pendingPostsCount = Post::where('status', '=', 'pending')->count();
    $this->rejectedPostsCount = Post::where('status', '=', 'rejected')->count();
    $this->expiredPostsCount = Post::where('status', '=', 'expired')->count();
    $this->archivedPostsCount = Post::where('status', '=', 'archived')->count();

    $this->allUsersCount = User::all()->count();
    $this->kycVerifiedUsersCount = User::whereHas('verification', function($query){
        $query->where('kyc_status', '=', 'verified');
    })->count();
    $this->pendingKycUsersCount = User::whereHas('verification', function($query){
        $query->where('kyc_status', '=', 'pending');
    })->count();
    $this->kycForApproval = User::where('email_verified_at', '=', null)->count();
    $this->flaggedUsersCount = User::where('status', '=', 'flagged')->count();

    $this->confirmedCreditOrdersCount = Order::where('status', '=', 'paid')->count();
    $this->pendingCreditOrdersCount = Order::where('status', '=', 'pending')->count();

    $this->openTicketsCount = Ticket::where('status', '=', 'open')->count();
    $this->pendingTicketsCount = Ticket::where('status', '=', 'pending')->count();
    $this->closedTicketsCount = Ticket::where('status', '=', 'closed')->count();

    }
};
