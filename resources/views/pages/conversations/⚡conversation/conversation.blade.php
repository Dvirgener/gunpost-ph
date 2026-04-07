<div class="flex flex-col justify-end h-full">
    @if ($conversation)
        {{-- <span>{{ $unreadCount }}</span> --}}
        <div>
            @if ($conversation->post)
                <span class="font-bold">Post : </span>
                {{ $conversation->post->title }}
            @endif

        </div>
        <div x-data="{
            scrollToBottom() {
                $nextTick(() => {
                    const box = $refs.commentBox;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        }" x-init="scrollToBottom()" x-on:message-added.window="scrollToBottom()"
            class="flex-1 flex flex-col overflow-y-scroll overflow-x-hidden pe-3 h-full mb-4" x-ref="commentBox">

            <flux:spacer />
            @foreach ($conversation->messages as $message)
                <livewire:pages::conversations.message-card :message="$message" :key="$message->id" />
            @endforeach
            {{-- @if (!empty($files))
        <template x-for="(file, index) in files" :key="index" class="mb-2">
            <div class="flex items-center justify-between gap-4 border p-2 rounded shadow-sm mb-2">
                <div class="flex items-center gap-4 mb-2">
                    <template x-if="file.type.startsWith('image/')">
                        <img :src="getUrl(file)" class="w-16 h-16 object-cover rounded" />
                    </template>
                    <template x-if="!file.type.startsWith('image/')">
                        <div
                            class="w-16 h-16 flex items-center justify-center bg-gray-200 text-gray-600 rounded text-2xl">
                            <span x-text="getIcon(file.name)"></span>
                        </div>
                    </template>
                    <div class="text-sm">
                        <p class="font-medium truncate" x-text="file.name"></p>
                        <p class="text-gray-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                    </div>
                </div>
            </div>
        </template>
        @endif --}}
        </div>
        <div class="w-full">
            <div class="" x-data x-on:send-reply.window=" $refs.msgInput.focus()">
                <form action="" wire:submit="addMessage" id="" enctype="multipart/form-data"
                    class="flex w-full gap-2">
                    <div class="flex w-full justify-between items-center gap-2">
                        <flux:input wire:model="message" x-ref="msgInput" />
                    </div>
                    <button class="hover:cursor-pointer bg-purple-500 p-2 rounded-md ext-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
