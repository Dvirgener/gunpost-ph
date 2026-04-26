<?php

use App\Events\sendMessage;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Flux\Flux;

new class extends Component
{
    use WithFileUploads;

    public $conversationId = null ;

    #[Rule(['required'])]
    public $message = '';

    #[Rule(['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf'])]
    public $messageAttachment;

    public $isReply = false;
    public $replyMessageId = null;

    public $unreadCount;

    public function getListeners()
    {
        // if (! $this->conversationId) {
        //     return [
        //         'send-reply' => 'sendReply',
        //     ];
        // }


        return [
            "echo-private:conversation.{$this->conversationId},.new-message-event" => 'refThis',
            'send-reply' => 'sendReply',
        ];
    }


    public function mount(Conversation $conversation)
    {
        $this->conversationId = $conversation->id;
        $this->dispatch('message-added');
    }

    #[Computed]
    public function conversation()
    {
        if (! $this->conversationId) {
            return null;
        }

        return Conversation::with([
            'post',
            'participants',
            'messages' => fn ($q) => $q->orderBy('created_at'),
        ])->find($this->conversationId);
    }

    public function refThis($event = null)
    {
        unset($this->conversation);

        $this->dispatch('message-added');
        $this->dispatch('refreshThis');
    }

    public function sendReply($id)
    {
        $this->isReply = true;
        $this->replyMessageId = $id['id'];
    }

    public function addMessage()
    {

        $this->validate();

        $message = new Message();
        $message->conversation_id = $this->conversationId;
        $message->sender_id = auth()->id();
        $message->body = $this->message;
        $message->read_by = [auth()->id()];

        if ($this->isReply) {
            $message->is_reply = true;
            $message->message_id = $this->replyMessageId;
        }

        $message->save();

        if ($this->messageAttachment) {
            $this->validateOnly('messageAttachment');
            $this->uploadAttachment($message);
        }

        Conversation::where('id', $this->conversationId)->update([
            'updated_at' => now(),
        ]);

        $this->message = '';
        $this->messageAttachment = null;
        $this->isReply = false;
        $this->replyMessageId = null;

        unset($this->conversation);

        $this->dispatch('message-added');
        $this->dispatch('refreshThis');

        sendMessage::dispatch($message);
    }

    private function uploadAttachment(Message $message)
    {
        if ($this->messageAttachment) {
            $path = $this->messageAttachment->store(
                'conversations/attachments/' . $this->conversationId,
                'public'
            );

            $message->attachment_name = $this->messageAttachment->getClientOriginalName();
            $message->attachment_type = $this->messageAttachment->getClientMimeType();
            $message->attachment_path = $path;
            $message->save();
        }
    }
};
