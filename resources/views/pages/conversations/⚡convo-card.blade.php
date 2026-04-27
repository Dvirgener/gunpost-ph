<?php

use Livewire\Component;
use App\Models\Conversation;
use Jenssegers\Agent\Agent;

new class extends Component {
    public function getListeners()
    {
        return [
            "echo-private:conversation.{$this->convo->id},.new-message-event" => 'refreshConvoBox',
        ];
    }

    public $convo;
    public $selectedConvo;
    public $isMobile;

    public function mount($convo, $selectedConvo = null)
    {
        $this->convo = $convo;
        $this->selectedConvo = $selectedConvo;
        $this->isMobile = $this->getIsMobileProperty();
    }

    public function refreshConvoBox()
    {
        $this->convo->load(['messages' => fn($q) => $q->orderBy('created_at')]);
    }

    public function seeConversation(Conversation $conversation)
    {
        if($this->selectedConvo == $conversation){
            $this->selectedConvo = null;
        }else{
            $this->selectedConvo = $conversation;
        }


        if ($this->isMobile) {
            return redirect(route('mobile.conversation', ['conversation' => $conversation]));
        } else {
            $this->dispatch('seeConversation', ['conversation' => $conversation]);
        }
    }

    private function checkCon(){
        if($this->selectedConvo == $conversation){
            $this->selectedConvo = null;
        }else{
            $this->selectedConvo = $conversation;
        }

    }

    public function deleteConvo(Conversation $conversation)
    {
        $conversation->participants()->detach(auth()->user()->id);
        if ($conversation->participants()->count() == 0) {
            $conversation->messages()->delete();
            $conversation->delete();
        }

        $this->dispatch('refreshConversations');
    }

    private function getIsMobileProperty()
    {
        return (new Agent())->isMobile();
    }
};
?>

<div
    class="flex items-center justify-start space-x-3 relative">
    <div
        class="flex justify-between  gap-4 items-center capitalizetext-sm  hover:cursor-pointer px-2 py-1 rounded-md w-full relative">


        <div class="flex flex-col gap-2 w-full " wire:click="seeConversation('{{ $convo->id }}')">

            <div class="flex gap-2">
                <div class="font-bold text-sm flex items-center gap-2">
                    @if ($convo->unreadCountFor(auth()->user()->id) > 0)
                        <span class=" rounded-full px-3 py-1 text-[12px] bg-blue-400 absolute -top-2 -right-2">
                            {{ $convo->unreadCountFor(auth()->user()->id) }}
                        </span>
                    @endif
                </div>

                <div class="">
                    @switch($convo->type)
                        @case('post')
                            <flux:badge size="sm" color="orange" class="px-5 text-8! font-extrabold">Post</flux:badge>
                        @break

                        @case('direct')
                            <flux:badge color="green" size="sm" class="px-5 text-[8px] font-extrabold">DM
                            </flux:badge>
                        @break

                        @case('admin')
                            <flux:badge color="purple" size="sm" class="px-5 text-[10px] font-extrabold">Admin
                            </flux:badge>
                        @break

                        @default
                    @endswitch
                </div>
                <div class="flex -space-x-2">
                    @foreach ($convo->participants as $participant)
                        <flux:tooltip content="{{ $participant->first_name }}">
                            <img src="{{ url($participant->avatar_path ? 'storage/' . $participant->avatar_path : asset('/blank_image.png')) }}"
                                style="width:25px; height:25px" class=" rounded-full" alt="">
                        </flux:tooltip>
                    @endforeach
                </div>
            </div>
            <div class="ps-2 w-full">
                <p class="line-clamp-1 text-xs">{{ $convo->mainComponentMessages->first()->body }}
                </p>
            </div>


        </div>
        <div>
            <flux:dropdown>
                <flux:button variant="subtle" class="hover:cursor-pointer text-center">
                    <flux:icon.ellipsis-vertical />
                </flux:button>
                <flux:menu>
                    {{-- <flux:menu.item icon="plus">New post</flux:menu.item>
                                        <flux:menu.separator /> --}}
                    <flux:menu.item variant="danger" icon="trash" wire:click="deleteConvo('{{ $convo->id }}')">
                        Delete</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>

    </div>


</div>
