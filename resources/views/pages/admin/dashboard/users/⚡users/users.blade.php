<div>
    <div class="flex justify-between">
        <flux:heading size="lg" class="font-mono text-start my-2 font-bold">USERS</flux:heading>
        <flux:button color="red" variant="primary" class="hover:cursor-pointer" href="{{ route('dashboard') }}">
            Back
        </flux:button>
    </div>
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-4 w-full">
            <button wire:click="updateUserFilter('all')"
                class="border rounded-md {{ $userFilter == 'all' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="All Users" numbers="{{ $allUsers }}" />
            </button>
            <button wire:click="updateUserFilter('active')"
                class="border rounded-md {{ $userFilter == 'active' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Active" numbers="{{ $approvedUsers }}" />
            </button>
            <button wire:click="updateUserFilter('pending')"
                class="border rounded-md {{ $userFilter == 'pending' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Pending" numbers="{{ $pendingUsers }}" />
            </button>
            <button wire:click="updateUserFilter('flagged')"
                class="border rounded-md {{ $userFilter == 'flagged' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Flagged" numbers="{{ $flaggedUsers }}" />
            </button>
        </div>
    </div>
    <div class="flex justify-between gap-3 mb-2">

        <flux:radio.group wire:model.live="filterClassification" label="User Classification" variant="pills">
            <flux:radio value="all" label="All" />
            <flux:radio value="personal" label="Personal" />
            <flux:radio value="corporate" label="Corporate" />
        </flux:radio.group>
        {{-- * Search Box --}}
        <flux:input placeholder="Search for a user ..." wire:model.live="search" class="w-full sm:max-w-sm "
            icon="magnifying-glass" label="Search" clearable />

    </div>
    <div>
        <flux:pagination :paginator="$this->users" />
    </div>
    <div>
        <flux:table>
            <flux:table.columns>

                <flux:table.column>Picture</flux:table.column>
                <flux:table.column>Full Name</flux:table.column>
                <flux:table.column>Date Registered</flux:table.column>
                <flux:table.column>Status</flux:table.column>


                <flux:table.column class=""><span class="text-center w-full">
                        Post Credits</span>
                </flux:table.column>
                <flux:table.column>Actions</flux:table.column>

            </flux:table.columns>

            <flux:table.rows>

                @foreach ($this->users as $user)
                    <flux:table.row>

                        <flux:table.cell>

                            <flux:avatar size="sm"
                                src="{{ $user->picture ? url('storage/' . $user->picture) : asset('/blank_image.png') }}"
                                :name="$user->first_name" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">

                                <span class="font-semibold"> {{ $user->first_name }}</span>

                                {{-- @if ($user->documents->isNotEmpty() && $user->status == 'pending')
                                    <flux:badge color="green" size="xs" inset="top bottom" class="text-xs">
                                        Documents Uploaded
                                    </flux:badge>
                                @endif --}}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $user->created_at->format('F j, Y') }}</flux:table.cell>
                        <flux:table.cell>
                            @switch($user->status)
                                @case('pending')
                                    <flux:badge color="orange" size="sm" inset="top bottom" class="capitalize">
                                        {{ $user->status }}</flux:badge>
                                @break

                                @case('active')
                                    <flux:badge color="green" size="sm" inset="top bottom" class="capitalize">
                                        {{ $user->status }}</flux:badge>
                                @break

                                @case('flagged')
                                    <flux:badge color="red" size="sm" inset="top bottom" class="capitalize">
                                        {{ $user->status }}</flux:badge>
                                @break

                                @default
                                    <flux:badge color="lime" size="sm" inset="top bottom" class="capitalize">Admin
                                    </flux:badge>
                            @endswitch

                        </flux:table.cell>

                        <flux:table.cell variant="strong" class="text-center">
                            {{ $user->post_credits }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            <flux:dropdown>
                                <flux:button icon:trailing="chevron-down"></flux:button>

                                <flux:menu>
                                    {{-- <flux:menu.item variant="default" icon="user" class="hover:bg-amber-600 "> --}}
                                    {{-- <a href=" {{ route('other.profile', ['user' => $user->uuid]) }}"
                                            class="">
                                            View Profile
                                        </a> --}}
                                    {{-- </flux:menu.item> --}}

                                    <flux:menu.item variant="default" icon="plus" class="hover:cursor-pointer"
                                        wire:click="openAddCreditModal('{{ $user->uuid }}')">
                                        Add Credits
                                    </flux:menu.item>

                                    @if ($user->status == 'flagged')
                                        <flux:menu.item variant="default" icon="check" class="hover:cursor-pointer"
                                            wire:click="unflagUser('{{ $user->uuid }}')">
                                            Unflag User
                                        </flux:menu.item>
                                    @else
                                        <flux:menu.item variant="danger" icon="exclamation-triangle"
                                            class="cursor-pointer" wire:click="flagUser('{{ $user->uuid }}')">
                                            Flag
                                        </flux:menu.item>
                                    @endif


                                    {{-- <flux:menu.item variant="danger" icon="trash" class="cursor-pointer">
                                    Delete
                                </flux:menu.item> --}}

                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>


    </div>



    <flux:modal name="add-credit-modal" class="md:w-96">
        <livewire:pages::admin.dashboard.users.add-credit />
    </flux:modal>
</div>
