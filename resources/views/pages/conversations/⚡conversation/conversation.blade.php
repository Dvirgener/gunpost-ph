@placeholder
    <div class="h-full flex items-center justify-center">
        <flux:icon.loading />
    </div>
@endplaceholder

<div class="h-full flex flex-col">
    @if ($this->conversation)
        <div class="w-full shrink-0">
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
                @case('admin')
                    <flux:card size="sm" class="mb-2 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                        <flux:heading class="mb-2 flex items-center gap-2">
                            Admin Thread:
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
                {{-- <form wire:submit="addMessage" enctype="multipart/form-data">
                    @if ($isDisabled)
                        <div class="flex justify-center text-xs italic text-red-600/70 mb-1">
                            <span>There are no other participants in this this->conversation...</span>
                        </div>
                    @endif

                    <div
                        class="w-full rounded-md p-2 outline outline-gray-200 dark:outline-gray-600 {{ $isDisabled ? 'outline-1 outline-red-500' : 'outline-1' }}">


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
                </form> --}}

                <form wire:submit="addMessage">

                    <input type="file" class="hidden" id="attachment" accept=".png,.gif,.jpeg,.jpg"
                                {{ $isDisabled ? 'disabled' : '' }} wire:model="messageAttachment">

                    <flux:composer wire:model="message" label="Prompt" label:sr-only placeholder="Type your message ..." :disabled="$isDisabled">
                        <x-slot name="actionsLeading">


                            <label for="attachment" class="rounded-md p-2 text-sm transition-all duration-150 hover:cursor-pointer hover:scale-105 hover:bg-white/20">
                                <flux:icon.paper-clip size="xs" />
                            </label>

                            <div>
                                @if ($messageAttachment)
                                    <div class="mb-2">
                                        <img src="{{ $messageAttachment->temporaryUrl() }}" alt="Preview"
                                            class="max-h-30 rounded-md">
                                    </div>
                                @endif
                            </div>

                        </x-slot>

                        <x-slot name="actionsTrailing">

                            <flux:button type="submit" size="sm" variant="primary" icon="paper-airplane" />
                        </x-slot>
                    </flux:composer>
                </form>
            </div>
        </div>
    @endif
</div>
