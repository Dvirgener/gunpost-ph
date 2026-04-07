<div>
    <div class="flex justify-between mb-3">
        <h1 class="font-bold text-2xl">MESSAGES</h1>
    </div>
    <div class="w-full">
        <div class="flex items-end justify-start gap-3 mb-3">

            <flux:input icon="magnifying-glass" placeholder="Search..." class="w-80!" label="Search messages"
                wire:model.live="search" clearable />

            <flux:select variant="listbox" searchable placeholder="Select Specific post..." label="Filter by post"
                class="w-100!" wire:model.live="filterPost" clearable>
                @foreach (auth()->user()->posts as $post)
                    <flux:select.option value="{{ $post->id }}">{{ $post->title }}</flux:select.option>
                @endforeach
            </flux:select>
            @if (auth()->user()->account_type != 'TFT_admin')
                <flux:modal.trigger name="sendAdminMessage">
                    <flux:button icon="ticket" class="text-red-700! dark:text-red-500! hover:cursor-pointer">Message
                        Admin
                    </flux:button>
                </flux:modal.trigger>
            @endif

        </div>
    </div>
    <div class="flex gap-10 px-2 mb-4 border-b pb-4">

        <flux:radio.group wire:model.live="filter" label="Conversation Filter" variant="pills">
            <flux:radio value="all" label="All" />
            <flux:radio value="unread" label="Unread" />
            <flux:radio value="archive" label="Archive" />
        </flux:radio.group>

        <flux:radio.group wire:model.live="filterCategory" label="Categories" variant="pills">
            <flux:radio value="all" label="All" />
            <flux:radio value="post" label="Posts" />
            <flux:radio value="direct" label="Direct Messages" />
            <flux:radio value="admin" label="Admin" />
        </flux:radio.group>

    </div>
    <div class="flex gap-3">
        <div class="w-1/2 px-3 py-2 rounded">
            <flux:heading>
                Conversations
            </flux:heading>
            <div class="h-150 overflow-y-scroll pe-3">
                @forelse ($this->conversations as $convo)
                    <div
                        class="flex items-center mb-2 mt-2 pb-2 justify-start space-x-3 border-b border-gray-300 dark:border-gray-700">
                        <button wire:click="deleteConvo('{{ $convo->id }}')"
                            wire:confirm="Are you sure you want to delete this conversation?" class="">
                            <flux:icon.trash color="gray" variant="micro"
                                class="hover:cursor-pointer hover:scale-125 hover:shadow-lg hover:text-red-500 transition-all duration-300 ease-in-out" />
                        </button>
                        <div class="flex gap-4 py-2 items-end capitalizetext-sm  hover:cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-50/10 p-2 rounded-md transition-all duration-200 w-full relative shadow"
                            wire:click="seeConversation('{{ $convo->id }}')">

                            <div class="absolute top-1 right-1">
                                @switch($convo->type)
                                    @case('post')
                                        <flux:badge size="sm" color="orange" class="px-5">Post</flux:badge>
                                    @break

                                    @case('direct')
                                        <flux:badge size="sm" color="green" class="px-5">DM</flux:badge>
                                    @break

                                    @default
                                @endswitch
                            </div>





                            <div class="flex flex-col gap-2">
                                <div class="font-bold text-sm flex items-center gap-2">
                                    @if ($convo->unread_count)
                                        <span class="font-mono text-blue-400">
                                            {{ '(' . $convo->unread_count . ')' }}
                                        </span>
                                    @endif
                                    {{-- <div>
                                        <span>
                                            @switch($convo->type)
                                                @case('post')
                                                    {{ $convo->post->title }}
                                                @break

                                                @case('admin')
                                                    @if (auth()->user()->classification == 'TFT_admin')
                                                        {{ $convo->initiator->fullName() }}
                                                    @else
                                                        Admin
                                                    @endif
                                                @break

                                                @default
                                            @endswitch
                                        </span>
                                    </div> --}}
                                </div>
                                <div class="flex space-x-2 items-end">
                                    <span
                                        class="line-clamp-1 text-xs">{{ $convo->mainComponentMessages->first()->body }}</span>
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

                        </div>


                    </div>




                    @empty
                        <div class="text-center text-gray-500 mt-10 italic">
                            -- No conversations found --
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="w-full h-155">
                <livewire:pages::conversations.conversation />
            </div>
        </div>
    </div>
