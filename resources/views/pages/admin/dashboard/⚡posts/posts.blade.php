<div>
    <div class="flex justify-between">
        <flux:heading size="lg" class="font-mono text-start my-2 font-bold">POSTS</flux:heading>
        <flux:button color="red" variant="primary" class="hover:cursor-pointer" href="{{ route('dashboard') }}">
            Back
        </flux:button>
    </div>
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 my-4 w-full">

            <button wire:click="updatePostFilter('all')"
                class="border rounded-md {{ $postFilter == 'all' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="All Posts" numbers="{{ $allPostCount }}" />
            </button>
            <button wire:click="updatePostFilter('approved')"
                class="border rounded-md {{ $postFilter == 'approved' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="approved" numbers="{{ $publishedPostCount }}" />
            </button>
            <button wire:click="updatePostFilter('pending')"
                class="border rounded-md {{ $postFilter == 'pending' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Pending" numbers="{{ $draftPostCount }}" />
            </button>
            <button wire:click="updatePostFilter('expired')"
                class="border rounded-md {{ $postFilter == 'expired' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Expired" numbers="{{ $closedPostCount }}" />
            </button>
            <button wire:click="updatePostFilter('archived')"
                class="border rounded-md {{ $postFilter == 'archived' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Archived" numbers="{{ $archivedPostCount }}" />
            </button>
            <button wire:click="updatePostFilter('flagged')"
                class="border rounded-md {{ $postFilter == 'flagged' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Flagged" numbers="{{ $flaggedPostCount }}" />
            </button>
        </div>
    </div>
    <div class="mb-2">

        <div class="flex justify-between">
            {{-- * Filter Post Types --}}
            <flux:radio.group wire:model.live="postTypeFilter" label="Post Types" variant="pills" class="mb-3">
                <flux:radio value="all" label="All" />
                <flux:radio value="sell" label="Sell" />
                <flux:radio value="buy" label="Buy" />
            </flux:radio.group>

            {{-- * Search Box --}}
            <flux:input placeholder="Search for postings ..." wire:model.live="search" class="w-full sm:max-w-sm "
                icon="magnifying-glass" label="Search" clearable />
        </div>
    </div>
    <div>
        <div>
            <flux:pagination :paginator="$this->posts" />
        </div>
    </div>
    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Date Drafted</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Statistics</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->posts as $post)
                    <flux:table.row>
                        <flux:table.cell>
                            <a href="" class="">
                                <div class="flex items-center gap-2 hover:font-bold">
                                    <flux:tooltip content="{{ $post->user->first_name }}">
                                        <flux:avatar size="sm"
                                            src="{{ $post->user->avatar_path ? url('storage/' . $post->user->avatar_path) : asset('blank_image.png') }}"
                                            :name="$post->user->first_name" />
                                    </flux:tooltip>
                                </div>
                            </a>
                        </flux:table.cell>
                        <flux:table.cell>
                            @switch($post->listing_type)
                                @case('sell')
                                    <flux:badge color="purple" class="">Sell</flux:badge>
                                @break

                                @case('buy')
                                    <flux:badge color="red">Buy</flux:badge>
                                @break

                                @default
                            @endswitch
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-virg.admin.post-category :category="$post->category" />
                        </flux:table.cell>
                        <flux:table.cell>{{ $post->title }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $post->created_at?->timezone('Asia/Manila')->format('M d, Y A') ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="gap-2">
                            <x-virg.admin.post-status :status="$post->status" />
                        </flux:table.cell>
                        <flux:table.cell variant="strong" class="flex gap-4">
                            <div class="flex items-center gap-2">
                                <flux:tooltip content="Inquiries">
                                    <a href="chats?filterPost={{ $post->id }}">
                                        <flux:icon.chat-bubble-left-right />
                                    </a>
                                </flux:tooltip>
                                {{-- <span>{{ $post->conversations->count() }}</span> --}}
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:tooltip content="Views">
                                    <flux:icon.eye class="inline-block" class="hover:cursor-pointer" />
                                </flux:tooltip>
                                <span class="font-bold text-sm">{{ $post->views ?? 0 }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button icon:trailing="chevron-down"></flux:button>

                                <flux:menu>
                                    <flux:menu.item variant="default" icon="eye" class="hover:bg-amber-600 ">
                                        <a href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
                                            class="">
                                            View Post
                                        </a>
                                    </flux:menu.item>
                                    @if ($post->status == 'pending')
                                        <flux:menu.item variant="default" icon="check"
                                            class="hover:bg-amber-600 hover:cursor-pointer"
                                            wire:click="approvePost('{{ $post->id }}')">
                                            Approve
                                        </flux:menu.item>
                                        <flux:menu.item variant="danger" icon="trash" class="cursor-pointer"
                                            wire:click="denyPost('{{ $post->id }}')">
                                            Deny
                                        </flux:menu.item>
                                    @endif


                                    @if ($post->status == 'flagged')
                                        <flux:menu.item variant="default" icon="check" class="hover:cursor-pointer"
                                            wire:click="unflagPost('{{ $post->id }}')">
                                            Unflag post
                                        </flux:menu.item>
                                    @else
                                        <flux:menu.item variant="danger" icon="exclamation-triangle"
                                            class="cursor-pointer" wire:click="flagPost('{{ $post->id }}')">
                                            Flag
                                        </flux:menu.item>
                                    @endif


                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach

            </flux:table.rows>
        </flux:table>
    </div>
</div>
