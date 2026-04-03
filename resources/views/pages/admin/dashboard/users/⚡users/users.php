<?php

use Livewire\Component;
use App\Models\user\User;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component
{
        use WithPagination;

    public $allUsers, $pendingUsers, $approvedUsers, $flaggedUsers;

    public $userFilter = 'pending';
    public $filterClassification = 'all';
    public $search;

    #[On('refreshList')]
    public function mount()
    {
        $this->allUsers = User::count();
        $this->pendingUsers = User::where("status", "pending")->count();
        $this->approvedUsers = User::where("status", "active")->count();
        $this->flaggedUsers = User::where("status", "flagged")->count();
    }
    public function approveUser(User $user)
    {
        $user->status = 'verified'; // Change the user's status to approved
        $user->save(); // Save the changes to the database
        Flux::toast(
            heading: 'User Approved!',
            text: 'You have successfully approved the user.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
    }

    public function flagUser(User $user)
    {
        $user->status = 'flagged'; // Change the user's status to flagged
        $user->save(); // Save the changes to the database
        Flux::toast(
            heading: 'User Flagged!',
            text: 'You have successfully flagged the user.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
    }

    public function unflagUser(User $user)
    {
        $user->status = 'verified'; // Change the user's status to verified
        $user->save(); // Save the changes to the database
        Flux::toast(
            heading: 'User Unflagged!',
            text: 'You have successfully unflagged the user.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
    }

    public function updateUserFilter($filter)
    {
        $this->userFilter = $filter;
    }


    public function openAddCreditModal(User $user)
    {
         Flux::modal('add-credit-modal')->show();
         $this->dispatch('selectedUserClicked', $user->uuid);
    }

    #[Computed()]
    public function users()
    {
        return User::orderBy('created_at', 'asc')
            ->when($this->userFilter != 'all', function ($query) {
                return $query->where('status', $this->userFilter);
            })
            ->when($this->filterClassification != 'all', function ($query) {
                return $query->where('account_type', $this->filterClassification);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(5);
    }
};
