<?php

use Livewire\Component;
use app\Models\user\User;

new class extends Component
{
    public $profile;

    public function mount(User $user)
    {
        // This is the profile page, so we will just pass the authenticated user to the details component.
        if($user->id == auth()->user()->id) {
            $this->profile = auth()->user() ;
        } else {
            $this->profile = $user;
        }

    }

    public function verifyUser(){

        if(auth()->user()->isAdmin()){

            $this->profile->verification->update(
                [
                    'reviewed_by' => auth()->user()->id,
                    'kyc_status' => 'verified',
                    'kyc_notes' => 'Verified by admin through profile page.'
                ]
            );
        }
    }
};
