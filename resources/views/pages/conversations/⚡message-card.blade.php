<?php

use Livewire\Component;
use App\Models\Message;
use Livewire\Attributes\On;

new class extends Component {
    public $message;
    public $replying;

    public function mount(Message $message)
    {
        $this->message = $message;
    }

    public function sendReply($id)
    {
        $this->replying = !$this->replying;
        $this->dispatch('send-reply', ['id' => $id]);
    }

    #[On('message-added')]
    public function res()
    {
        $this->replying = '';
    }
};
?>

<div class="mb-5">
    <div class="" wire:key="{{ $message->id }}">

        @if ($message->sender_id != Auth::user()->id)
            <div class="flex flex-col items-start justify-end">
                <div class="flex items-start gap-1">
                    <div class="w-12">
                        {{-- * Show the Image of the User who made the Comment --}}
                        <flux:tooltip content="{{ $message->sender->first_name }}">
                            <img src="{{ url($message->sender->avatar_path ? 'storage/' . $message->sender->avatar_path : asset('/blank_image.png')) }}"
                                class="w-8 h-8 rounded-full" alt="">
                        </flux:tooltip>
                    </div>

                    <div class="w-full">
                        @if ($message->is_reply)
                            <div class="relative py-2 bg-purple-500 opacity-30 p-3  rounded-md">
                                <p class="text-sm text-left text-white"> {{ $message->replyTo->body }}</p>
                            </div>
                        @endif
                        <div class="py-1 px-4 rounded-md flex gap-1 items-start w-full">
                            <div
                                class="py-1 px-4 w-full rounded-md  {{ $replying ? 'bg-blue-400' : 'bg-neutral-600' }} flex flex-col gap-1 items-start">
                                @if ($message->attachment_path)
                                    <img src="{{ url($message->attachment_path) }}" alt="" class="w-full">
                                @endif

                                <p class="text-sm text-left text-white w-fit max-w-xs">{{ $message->body }}
                                </p>
                                <span
                                    class="text-[10px] text-gray-300">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <flux:tooltip content="reply">
                                <button class="text-xs hover:cursor-pointer"
                                    wire:click="sendReply('{{ $message->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        class="size-3">
                                        <path fill-rule="evenodd"
                                            d="M12.5 9.75A2.75 2.75 0 0 0 9.75 7H4.56l2.22 2.22a.75.75 0 1 1-1.06 1.06l-3.5-3.5a.75.75 0 0 1 0-1.06l3.5-3.5a.75.75 0 0 1 1.06 1.06L4.56 5.5h5.19a4.25 4.25 0 0 1 0 8.5h-1a.75.75 0 0 1 0-1.5h1a2.75 2.75 0 0 0 2.75-2.75Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </button>
                            </flux:tooltip>
                        </div>

                    </div>


                </div>

            </div>
        @else
            <div class="flex flex-col justify-end items-end ps-13 w-full">
                @if ($message->is_reply)
                    <div class="relative py-1 px-4 bg-neutral-500 opacity-50 p-3  rounded-md">
                        <p class="text-sm text-left text-white"> {{ $message->replyTo->body }}</p>
                    </div>
                @endif

                <div class="py-1 px-4  rounded-md text-white bg-purple-500 flex flex-col gap-1">
                    @if ($message->attachment_path)
                        <img src="{{ url($message->attachment_path) }}" alt="" class="p-4 w-full">
                    @endif
                    <p class="text-sm text-start" dir="">{{ $message->body }}</p>
                    <span
                        class="text-[10px] text-gray-300 text-end w-full">{{ $message->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @endif

        <flux:spacer />
    </div>
</div>
