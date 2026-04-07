<div>
    <div class="px-2 my-3">
        <div class="mb-5 flex gap-2 items-center justify-between">
            <h1 class="font-bold text-sm">Posts</h1>
            <div class="md:hidden">
                @if ($profile->verification->kyc_status == 'pending' && !auth()->user()->isAdmin())
                    <flux:button variant="primary" color="gray" disabled="true" class="cursor-pointer">
                        Add Post</flux:button>
                @else
                    <flux:button variant="primary" color="orange" class="cursor-pointer"
                        href="{{ route('posts.create.index') }}">
                        Add Post</flux:button>
                @endif

            </div>

        </div>
        <div>
            <div class="flex justify-between items-end mb-5 gap-3">
                <div class="grid md:grid-cols-2 gap-1 w-full sm:w-auto space-y-2 md:space-y-0">

                    <flux:input placeholder="search by title or description..." wire:model.live="search"
                        class="w-full sm:max-w-sm" icon="magnifying-glass" label="Search" />

                    <div class="md:flex gap-5 space-y-2 md:space-y-0 w-full items-end ">
                        <flux:select wire:model.live="filterType" placeholder="Post Types" class="w-full"
                            label="Post Types">
                            <option value="">All</option>
                            <option value="sell">Sell</option>
                            <option value="buy">Buy</option>
                        </flux:select>

                        <flux:select wire:model.live="filterCategory" placeholder="Post Category" label="Post Category"
                            class="w-full">
                            <option value="">All</option>
                            <option value="gun">Guns</option>
                            <option value="ammunition">Ammunitions</option>
                            <option value="airsoft">Airsofts</option>
                            <option value="accessory">Accessories</option>
                            <option value="others">Others</option>
                        </flux:select>

                        <flux:select wire:model.live="filterStatus" placeholder="All Status" class="w-full"
                            label="Status">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="expired">Expired</option>
                            <option value="archived">Archived</option>
                            <option value="flagged">Flagged</option>
                        </flux:select>

                    </div>

                </div>

                <div class="hidden md:block">
                    @if (($profile->verification->kyc_status == 'pending' && !auth()->user()->isAdmin()) || !$profile->isMe())
                        <flux:button variant="primary" color="gray" disabled="true" class="cursor-pointer">
                            Add Post</flux:button>
                    @else
                        <flux:button variant="primary" color="orange" class="cursor-pointer"
                            href="{{ route('posts.create.index') }}">
                            Add Post</flux:button>
                    @endif

                </div>



            </div>
        </div>
        <div class="px-2">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Title</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Category</flux:table.column>
                    <flux:table.column>Date Created</flux:table.column>
                    <flux:table.column>Date Published</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    @if (auth()->user()->id == $profile->id)
                        <flux:table.column>Statistics</flux:table.column>
                    @endif

                    <flux:table.column><span class="w-full text-center">Action</span>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->posts as $post)
                        <flux:table.row>
                            <flux:table.cell class="w-70 hover:font-bold hover:cursor-pointer">
                                <a
                                    href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}">
                                    {{ $post->title }}
                                </a>
                            </flux:table.cell>
                            <flux:table.cell>
                                @switch($post->listing_type)
                                    @case('sell')
                                        <flux:badge color="purple" class="" size="sm">Sell</flux:badge>
                                    @break

                                    @case('buy')
                                        <flux:badge color="red" size="sm">Buy</flux:badge>
                                    @break

                                    @default
                                @endswitch
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-virg.admin.post-category category="{{ $post->category }}" />
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $post->created_at?->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $post->approved_at?->timezone('Asia/Manila')->format('M d, Y ') ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="gap-2">
                                <x-virg.admin.post-status :status="$post->status" />
                                @if ($post->status == 'expired' && auth()->user()->id == $profile->id)
                                    <flux:tooltip content="Renew Post (-1 Credit)">
                                        <flux:button variant="subtle" class="hover:cursor-pointer"
                                            wire:click="renewPost('{{ $post->id }}')">
                                            <flux:icon.refresh-ccw class="size-4" />
                                        </flux:button>
                                    </flux:tooltip>
                                @endif

                            </flux:table.cell>
                            @if (auth()->user()->id == $profile->id)
                                <flux:table.cell variant="strong" class="flex gap-4">
                                    <div class="flex items-center gap-2">
                                        <flux:tooltip content="Inquiries">
                                            <a href="chats?filterPost={{ $post->id }}">
                                                <flux:icon.chat-bubble-left-right />
                                            </a>
                                        </flux:tooltip>
                                        <span>{{ $post->conversations->count() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:tooltip content="Views">
                                            <flux:icon.eye class="inline-block" class="hover:cursor-pointer" />
                                        </flux:tooltip>
                                        <span class="font-bold text-sm">{{ $post->views ?? 0 }}</span>
                                    </div>
                                </flux:table.cell>
                            @endif
                            @if ($profile->isMe())
                                <flux:table.cell variant="strong">
                                    <div class="flex justify-end items-center gap-1">

                                        @if ($post->status !== 'closed')
                                            <flux:button
                                                href="{{ route('posts.edit.category.' . $post->category, $post->uuid) }}"
                                                variant="primary" color="cyan">
                                                <flux:icon.pencil-square variant="micro" />
                                            </flux:button>
                                        @endif

                                        <flux:button variant="primary" color="red"
                                            wire:click="deletePost('{{ $post->id }}')"
                                            wire:confirm="Are you sure you want to delete this post?"
                                            class="hover:cursor-pointer">
                                            <flux:icon.trash variant="micro" />
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            @endif

                        </flux:table.row>
                        @empty
                            <flux:table.cell colspan="6" class="text-center">
                                No posts found.
                            </flux:table.cell>
                        @endforelse

                    </flux:table.rows>
                </flux:table>
            </div>
            <div class="mb-3">
                <flux:pagination :paginator="$this->posts" />
            </div>

        </div>
    </div>
