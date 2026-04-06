<?php

use App\Models\user\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $owner;
    public $profileData = [];
    public $profileType;

    public $user_region;
    public $user_province;
    public $user_city;
    public $user_street;

    public $regions;
    public $provinces;
    public $cities;

    public $userDescription;

    public function mount(User $user)
    {
        $this->owner = $user->loadMissing(['personalProfile', 'corporateProfile']);
    }
};
