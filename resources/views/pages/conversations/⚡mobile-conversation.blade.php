<?php

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Flux\Flux;
use App\Events\sendMessage;

new class extends Component {

    use withFileUploads;

    public $conversationId ;
    #[Rule(['required'])]
    public $message = '';

    #[Rule(['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf'])]
    public $messageAttachment;

    public $isReply = false;
    public $replyMessageId = null;

    public $unreadCount;

    public function getListeners()
    {
        if (! $this->conversationId) {
            return [
                'send-reply' => 'sendReply',
            ];
        }

        return [
            "echo-private:conversation.{$this->conversationId},.new-message-event" => 'refThis',
            'send-reply' => 'sendReply',
        ];
    }

    public function mount(Conversation $conversation){
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

    public function back(){
        return redirect(route('conversations'));
    }
};
?>

<div class="flex flex-col h-full">
    <div class="w-full shrink-0 mb-2 flex justify-center items-between">
    @switch($this->conversation->type)
        @case('post')
            <a
                href="{{ route('posts.view.category.index', ['post' => $this->conversation->post->uuid, 'category' => $this->conversation->post->category]) }}">
                <flux:card size="sm" class="mb-2 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                    <flux:heading class="flex items-center gap-2">
                        Post : {{ $this->conversation->post->title }}
                        <flux:icon name="arrow-up-right" class="ml-auto text-zinc-400" variant="micro" />
                    </flux:heading>

                    <flux:text class="mt-2">
                        {{ $this->conversation->post->description }}
                    </flux:text>
                </flux:card>
            </a>
        @break

        @case('direct')
            <flux:card size="sm" class="mb-2 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                <flux:heading class="mb-2 flex items-center gap-2">
                    Direct Message:
                </flux:heading>

                <div>
                    @foreach ($this->conversation->participants->where('id', '!=', auth()->user()->id) as $participant)
                        <div class="flex items-center gap-3">
                            <flux:avatar
                                src="{{ url($participant->avatar_path ? 'storage/' . $participant->avatar_path : asset('/blank_image.png')) }}" />

                            <a href="{{ route('profile.visit', $participant->uuid) }}" class="hover:underline">
                                <flux:text>{{ $participant->fullName() }}</flux:text>
                            </a>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        @break
    @endswitch
    <div>
        <flux:button wire:click="back">Backs</flux:button>
    </div>
    </div>

    <div x-data="{
            scrollToBottom() {
                $nextTick(() => {
                    const box = $refs.commentBox;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        }" x-init="scrollToBottom()" x-on:message-added.window="scrollToBottom()"
            x-ref="commentBox" class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden pe-3 mb-4">
            <flux:spacer />
            @foreach ($this->conversation->messages as $message)
                <livewire:pages::conversations.message-card :message="$message" :key="'message-'.$message->id" />
            @endforeach
        </div>

        <div class="w-full shrink-0">
            <div x-data x-on:send-reply.window="$refs.msgInput.focus()">
                @php
                    if ($this->conversation->participants()->count() <= 1) {
                        $isDisabled = true;
                    } else {
                        $isDisabled = false;
                    }

                @endphp
                <form wire:submit="addMessage" enctype="multipart/form-data">
                    @if ($isDisabled)
                        <div class="flex justify-center text-xs italic text-red-600/70 mb-1">
                            <span>There are no other participants in this this->conversation...</span>
                        </div>
                    @endif

                    <div
                        class="w-full rounded-md p-2 outline outline-gray-200 dark:outline-gray-600 {{ $isDisabled ? 'outline-1 outline-red-500' : 'outline-1' }}">
                        @if ($messageAttachment)
                            <div class="mb-2">
                                <img src="{{ $messageAttachment->temporaryUrl() }}" alt="Preview"
                                    class="max-h-30 rounded-md">
                            </div>
                        @endif

                        <textarea x-ref="msgInput" cols="30" rows="3"
                            class="mb-2 w-full resize-none text-xs focus:outline-0 {{ $isDisabled ? 'text-red-500' : 'outline-0' }} "
                            placeholder="Write a Message..." wire:model="message" {{ $isDisabled ? 'disabled' : '' }}></textarea>

                        <div class="flex justify-between">
                            <input type="file" class="hidden" id="attachment" accept=".png,.gif,.jpeg,.jpg"
                                {{ $isDisabled ? 'disabled' : '' }} wire:model="messageAttachment">

                            <label for="attachment"
                                class="rounded-md p-2 text-sm transition-all duration-150 hover:cursor-pointer hover:scale-105 hover:bg-white/20">
                                <flux:icon.paper-clip variant="mini" />
                            </label>

                            <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane"
                                :disabled="$isDisabled" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
