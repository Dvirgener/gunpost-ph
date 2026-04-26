<?php

use Livewire\Component;
use app\Models\user\User;

new class extends Component
{
    public $profile;

    public function mount(User $user)
    {
        $this->profile = $user;

    }
};
