<div class="p-4 h-full overflow-visible relative">

    <div class="flex justify-between">
        <h1 class="font-bold text-2xl">POSTS</h1>
        <flux:button href="{{ route('posts.create.index') }}" class="hover:cursor-pointer font-bold! text-xs!">ADD POST
        </flux:button>
    </div>

    <div class="my-5 space-y-3 bg-gray-100/30 dark:bg-none p-4 rounded-md shadow-md outline-1 outline-gray-300">

        {{-- Category Filters --}}
        {{-- <flux:heading>Categories :</flux:heading> --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-6 gap-8 justify-around">
            <x-virg.posts.category-buttons label="All" />
            <x-virg.posts.category-buttons label="Guns" />
            <x-virg.posts.category-buttons label="Airsofts" />
            <x-virg.posts.category-buttons label="Ammunitions" />
            <x-virg.posts.category-buttons label="Accessories" />
            <x-virg.posts.category-buttons label="Others" />
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-start items-start gap-5 md:justify-between md:items-end">

        {{-- Filter Type --}}
        <flux:radio.group wire:model="postTypeFilter" label="" variant="segmented">
            <flux:radio label="All Posts" value="all" />
            <flux:radio label="Selling" value="sell" />
            <flux:radio label="Buying" value="buy" />
        </flux:radio.group>

        {{-- Search any keyword --}}
        <div class="flex flex-col gap-3">
            {{-- <flux:heading>Search:</flux:heading> --}}
            <flux:input type="text" icon="magnifying-glass" placeholder="Search..." class="rounded-md w-80!" />
        </div>
    </div>

    <div class="my-4">

        <flux:accordion variant="reverse">

            <flux:accordion.item>

                <flux:accordion.heading class=" p-2 shadow-md rounded-lg">
                    Additional Filters
                </flux:accordion.heading>

                <flux:accordion.content>
                    <div class="px-5">

                        <div class="space-y-3 mb-3">
                            <flux:heading>Set Price Range:</flux:heading>
                            <div class="flex gap-2">
                                <flux:input type="number" placeholder="Min Price" class="w-full rounded-md" />
                                <flux:input type="number" placeholder="Max Price" class="w-full rounded-md" />
                            </div>
                        </div>

                        <div class="mb-3 space-y-3">
                            <flux:heading>Filter by Location:</flux:heading>
                            <div class="grid grid-cols-3 w-full gap-2">
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

                                <flux:select wire:model="city" placeholder="Choose a City..." label="City">
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
    <div class="">
        <flux:pagination :paginator="$this->posts" />
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 p-2 h-140 overflow-y-scroll">
            @foreach ($this->posts as $post)
                <x-virg.posts.post-card :post="$post" />
            @endforeach
        </div>
    </div>


</div>
