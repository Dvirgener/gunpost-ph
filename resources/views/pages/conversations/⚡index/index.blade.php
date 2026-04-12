<div class="h-full min-h-0 flex flex-col">
    <div class="mb-3 flex justify-between shrink-0">
        <h1 class="text-2xl font-bold">MESSAGES</h1>
    </div>

    <div class="w-full shrink-0">
        <div class="mb-3 flex flex-col md:flex-row md:items-end md:justify-start gap-3">
            <flux:input icon="magnifying-glass" placeholder="Search..." class="w-80!" label="Search messages"
                wire:model.live="search" clearable />

            <flux:select variant="listbox" searchable placeholder="Select Specific post..." label="Filter by post"
                class="line-clamp-1" wire:model.live="filterPost" clearable>
                @foreach (auth()->user()->posts as $post)
                    <flux:select.option value="{{ $post->id }}">{{ $post->title }}</flux:select.option>
                @endforeach
            </flux:select>

            @if (auth()->user()->account_type != 'TFT_admin')
                <flux:modal.trigger name="sendAdminMessage">
                    <flux:button icon="ticket" class="text-red-700! dark:text-red-500! hover:cursor-pointer">
                        Message Admin
                    </flux:button>
                </flux:modal.trigger>
            @endif
        </div>
    </div>

    <div class="mb-4 flex flex-col md:flex-row gap-10 border-b px-2 pb-4 shrink-0">
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

    @if ($isMobile)
        <div class="min-h-0 flex-1 overflow-y-auto px-3">
            @forelse ($this->conversations as $convo)
                <livewire:pages::conversations.convo-card :convo="$convo" :key="'convo-card-' . $convo->id" />
            @empty
                <div class="mt-10 text-center italic text-gray-500">
                    -- No conversations found --
                </div>
            @endforelse
        </div>
    @else
        <div class="flex flex-1 min-h-0 gap-3">
            <div class="w-1/3 min-h-0 rounded px-3 py-2 flex flex-col">
                <flux:heading class="shrink-0">
                    Conversations
                </flux:heading>

                <div class="min-h-0 flex-1 overflow-y-auto px-3">
                    @forelse ($this->conversations as $convo)
                        <livewire:pages::conversations.convo-card :convo="$convo" :key="'convo-card-' . $convo->id" />
                    @empty
                        <div class="mt-10 text-center italic text-gray-500">
                            -- No conversations found --
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex-1 min-h-0 flex flex-col pt-5">
                @if ($selectedConversation)
                    <livewire:pages::conversations.conversation :conversation="$selectedConversation" :key="'conversation-' . $selectedConversation->id" lazy />
                @endif
            </div>
        </div>
    @endif


    {{-- Modal for Sending an Admin a Message --}}

    <flux:modal name="sendAdminMessage" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Admin Message</flux:heading>
                <flux:text class="mt-2">Send a message to the our Admin.</flux:text>
            </div>
            <form action="" wire:submit.prevent="sendAdminMessage" class="space-y-4">
                <flux:select variant="listbox" placeholder="Select admin..." class="py-1!" wire:model="chosenAdmin">
                    @foreach ($admins as $admin)
                        <flux:select.option value="{{ $admin->id }}">
                            <div class="flex items-center gap-2">
                                <flux:avatar
                                    src="{{ url($admin->avatar_path ? $admin->avatar_path : asset('blank_image.png')) }}" />
                                <flux:text>{{ $admin->fullName() }}</flux:text>

                            </div>
                        </flux:select.option>
                    @endforeach

                </flux:select>
                <flux:textarea label="Message" placeholder="Your message" wire:model="adminMessage" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Send Message</flux:button>
                </div>

            </form>

        </div>
    </flux:modal>
</div>
