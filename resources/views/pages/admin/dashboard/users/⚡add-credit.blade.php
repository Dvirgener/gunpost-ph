<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\user\User;
use Flux\Flux;

new class extends Component {
    public $user;
    public $credits;
    public $name;

    #[On('selectedUserClicked')]
    public function selectedUser(User $user)
    {
        $this->user = $user;
        $this->name = $user->first_name;
    }

    public function addCredits()
    {
        $this->user->post_credits += $this->credits;
        $this->user->save();
        Flux::modal('add-credit-modal')->close();
        $this->credits = null;
        $this->dispatch('refreshList');
        Flux::toast(heading: 'Credits Added!', text: 'You have successfully added credits to the user.', variant: 'success');
    }
};
?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Add Credits</flux:heading>
        <flux:text class="mt-2">Manually add credits to a user's account.</flux:text>
    </div>

    <form action="" class="space-y-5" wire:submit="addCredits">
        <flux:input label="User Name" placeholder="Your name" disabled wire:model="name" />

        <flux:input label="Number of Credits" type="number" wire:model="credits" />
        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Add Credits</flux:button>
        </div>
    </form>



</div>
