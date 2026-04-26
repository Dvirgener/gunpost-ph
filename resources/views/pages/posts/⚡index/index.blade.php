<div class="md:p-4 h-full flex flex-col">
    <x-virg.guest-callout />

    <div class="flex justify-between shrink-0">
        <h1 class="font-bold text-2xl">POSTS</h1>
        @auth
            @if (auth()->user()->post_credits <= 0)
                <flux:button disabled class="hover:cursor-not-allowed font-bold! text-xs! opacity-50!">ADD POST
                </flux:button>
            @else
                <flux:button class="hover:cursor-pointer font-bold! text-xs!" href="{{ route('posts.create.index') }}">ADD
                    POST
                </flux:button>
            @endif
        @endauth
    </div>

    <div class="flex-1 overflow-y-scroll flex flex-col min-h-0">
        @if (!$isMobile)
        <div class="my-5 space-y-3 bg-gray-100/30 dark:bg-none p-4 rounded-md shadow-md outline-1 outline-gray-300 shrink-0">
            {{-- Category Filters --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-8 justify-around">
                <x-virg.posts.category-buttons label="All" wire:click="filterByCategory('all')"
                    class="{{ $postCategory === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                <x-virg.posts.category-buttons label="Guns" wire:click="filterByCategory('gun')"
                    class="{{ $postCategory === 'gun' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                <x-virg.posts.category-buttons label="Airsofts" wire:click="filterByCategory('airsoft')"
                    class="{{ $postCategory === 'airsoft' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                <x-virg.posts.category-buttons label="Ammunitions" wire:click="filterByCategory('ammunition')"
                    class="{{ $postCategory === 'ammunition' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                <x-virg.posts.category-buttons label="Accessories" wire:click="filterByCategory('accessory')"
                    class="{{ $postCategory === 'accessory' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                <x-virg.posts.category-buttons label="Others" wire:click="filterByCategory('others')"
                    class="{{ $postCategory === 'others' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}" />
            </div>
        </div>
        @endif

        <div class="shrink-0">
            <flux:accordion variant="reverse" class="min-h-0 flex-1">
                <flux:accordion.item>
                    <flux:accordion.heading class=" p-2 shadow-md rounded-lg">
                        {{$isMobile ? 'Filters' : 'Additional Filters'}}
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="px-5 flex-1 min-h-0 shrink-0 overflow-y-auto flex flex-col">
                            @if ($isMobile)
                                <div class="my-5 space-y-3 bg-gray-100/30 dark:bg-none p-4 rounded-md shadow-md outline-1 outline-gray-300 shrink-0">
                                    {{-- Category Filters --}}
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-8 justify-around">
                                        <x-virg.posts.category-buttons label="All" wire:click="filterByCategory('all')"
                                            class="{{ $postCategory === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                                        <x-virg.posts.category-buttons label="Guns" wire:click="filterByCategory('gun')"
                                            class="{{ $postCategory === 'gun' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                                        <x-virg.posts.category-buttons label="Airsofts" wire:click="filterByCategory('airsoft')"
                                            class="{{ $postCategory === 'airsoft' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                                        <x-virg.posts.category-buttons label="Ammunitions" wire:click="filterByCategory('ammunition')"
                                            class="{{ $postCategory === 'ammunition' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                                        <x-virg.posts.category-buttons label="Accessories" wire:click="filterByCategory('accessory')"
                                            class="{{ $postCategory === 'accessory' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }}" />
                                        <x-virg.posts.category-buttons label="Others" wire:click="filterByCategory('others')"
                                            class="{{ $postCategory === 'others' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}" />
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-col md:flex-row justify-start items-start gap-5 md:justify-between md:items-end mb-5">
                                {{-- Filter Type --}}
                                <flux:radio.group wire:model.live="postTypeFilter" label="" variant="segmented">
                                    <flux:radio label="All Posts" value="all" />
                                    <flux:radio label="Selling" value="sell" />
                                    <flux:radio label="Buying" value="buy" />
                                </flux:radio.group>
                                {{-- Search any keyword --}}
                                <div class="flex flex-col gap-3">
                                    <flux:input type="text" icon="magnifying-glass" placeholder="Search..." class="rounded-md "
                                        wire:model.live="query_search" />
                                </div>
                            </div>

                            <div class="space-y-3 mb-3">
                                <flux:heading>Set Price Range:</flux:heading>
                                <div class="flex gap-2">
                                    <flux:input type="number" placeholder="Min Price" class="w-full rounded-md"
                                        wire:model.live="min_price" />
                                    <flux:input type="number" placeholder="Max Price" class="w-full rounded-md"
                                        wire:model.live="max_price" />
                                </div>
                            </div>

                            <div class="mb-3 space-y-3">
                                <flux:heading>Filter by Location:</flux:heading>
                                <div class="grid grid-cols-1 md:grid-cols-3 w-full gap-2">
                                    <flux:select wire:model.live="region" placeholder="Choose Region..." label="Region">

                                        @foreach ($regions as $region)
                                            <flux:select.option value="{{ $region['key'] }}">{{ $region['name'] }}
                                            </flux:select.option>
                                        @endforeach

                                    </flux:select>
                                    <flux:select wire:model.live="province" placeholder="Choose Province..."
                                        label="Province" class="">

                                        @foreach ($this->filteredProvinces as $province)
                                            <flux:select.option value="{{ $province['key'] }}">{{ $province['name'] }}
                                            </flux:select.option>
                                        @endforeach

                                    </flux:select>

                                    <flux:select wire:model.live="city" placeholder="Choose a City..." label="City">

                                        @foreach ($this->filteredCities as $city)
                                            <flux:select.option value="{{ $city['name'] }}">{{ $city['name'] }}
                                            </flux:select.option>
                                        @endforeach

                                    </flux:select>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

            </flux:accordion>

        </div>

        <div class="mb-2">
            <flux:pagination :paginator="$this->posts" />
        </div>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 p-2 flex-1 min-h-0 overflow-y-scroll">
            @foreach ($this->posts as $post)
                <x-virg.posts.post-card :post="$post" />
            @endforeach
        </div>
    </div>





</div>
