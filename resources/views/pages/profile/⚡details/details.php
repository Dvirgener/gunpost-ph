<?php

use App\Models\user\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Conversation;
use App\Models\Message;
use Flux\Flux;

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


    // METHOD FOR SENDING INQUIRY MESSAGE TO POST OWNER

    #[Rule(['required'])]
    public string $message = '';

    private function message(Conversation $convo)
    {
        Message::create([
            'conversation_id' => $convo->id,
            'sender_id' => auth()->user()->id,
            'body' => $this->message,
            'read_by' => [auth()->user()->id],
        ]);
    }

    public function sendMessage()
    {
        $this->validate();

        if ($this->message === '') {
            flux::toast('Message cannot be empty.', variant: 'danger');
            return;
        }

        $convo = Conversation::betweenUsers('direct', auth()->user()->id, $this->owner->id)->first();

        if ($convo) {
            $this->message($convo);
            flux::toast('Message sent successfully.', variant: 'success');
            Flux::modal('sendMessageModal')->close();
            $this->message = '';
            return;

        } else {
            $con = Conversation::create([
                'type' => 'direct',
                'initiator_id' => auth()->user()->id,
            ]);
            $con->participants()->sync([$this->owner->id, auth()->user()->id]);

            Message::create([
                'conversation_id' => $con->id,
                'sender_id' => auth()->user()->id,
                'body' => $this->message,
                'read_by' => [auth()->user()->id],
            ]);
            flux::toast('Message sent successfully.', variant: 'success');
            Flux::modal('sendMessageModal')->close();
            $this->message = '';
            return;
        }
    }


    public function mount(User $user)
    {
        $this->owner = $user->loadMissing(['personalProfile', 'corporateProfile']);
    }


};
