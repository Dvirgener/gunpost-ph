<?php

use App\Events\sendMessage;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $conversation;

    #[Rule(['required'])]
    public $message;

    #[Rule(['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf'])]
    public $messageAttachment;

    public $isReply = false;
    public $replyMessageId;

    public $unreadCount;

    public function getListeners()
    {

        return [
            "echo-private:conversation.{$this->conversation->id},.new-message-event" => 'refThis',
            'send-reply' => 'sendReply',
        ];
    }

    public function mount($conversation){
        $this->conversation = $conversation;
        $this->conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);
        $this->dispatch('message-added');
    }

    public function refThis($event = null)
    {
        $this->conversation->load(['messages' => fn ($q) => $q->orderBy('created_at')]);
        $this->dispatch('message-added');
        $this->dispatch('refreshThis');
    }

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

        if($this->messageAttachment){
            $this->validateOnly('messageAttachment');
            $this->uploadAttachment($message);
        }


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


        sendMessage::dispatch($message);

    }

    private function uploadAttachment(Message $message)
    {
        if ($this->messageAttachment) {
            $path = $this->messageAttachment->store('conversations/attachments/' . $this->conversation->id, 'public');
            $message->attachment_name = $this->messageAttachment->getClientOriginalName();
            $message->attachment_type = $this->messageAttachment->getClientMimeType();
            $message->attachment_path = $path;
            $message->save();
        }
    }
};
