<?php

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

new class extends Component
{
    public $conversation;

    #[Rule(['required'])]
    public $message;

    public $isReply = false;
    public $replyMessageId;

    public $unreadCount;

    #[On('open-convo')]
    public function convo(Conversation $conversation){

        $this->conversation = $conversation;
        $this->conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);
        $this->dispatch('message-added');
        $this->markIncomingAsRead();

    }

    private function markIncomingAsRead(): void
    {
        $unreadMessages = Message::where('conversation_id', '=', $this->conversation->id)->whereJsonDoesntContain('read_by', auth()->user()->id)->get();

        foreach($unreadMessages as $msg){
            $msg->markAsRead(auth()->user()->id);
        }
    }


    #[On('send-reply')]
    public function sendReply($id){
        $this->isReply = true;
        $this->replyMessageId = $id["id"];
        $this->conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);
    }




    public function addMessage(){
        $this->validate();

        $message = new Message();
        $message->conversation_id = $this->conversation->id;
        $message->sender_id = auth()->user()->id;
        $message->body = $this->message;
        $message->read_by = [auth()->user()->id];

        if($this->isReply){
            $message->is_reply = true;
            $message->message_id = $this->replyMessageId;
        }
        $message->save();

        $this->message = '';
        $this->isReply = false;
        $this->replyMessageId = null;

        // Make sure the new message appears in the foreach
        $this->conversation->unsetRelation('messages');
        $this->conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);

        $message->conversation->updated_at = now();
        $message->conversation->save();

        $this->dispatch('message-added');
        $this->dispatch('refreshThis');

    }
};
