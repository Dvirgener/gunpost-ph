
<div class="p-4 h-full overflow-visible">
    <h1 class="font-bold text-2xl">POSTS</h1>

    {{-- <flux:button href="{{ route('posts.create.index') }}" class="hover:cursor-pointer">ADD POST</flux:button> --}}
    {{-- @foreach ($this->posts as $post)
        {{ $post->title }}
    @endforeach --}}

    <div class="my-10 space-y-6">
        <flux:heading>Category Filter:</flux:heading>
        <div class="flex gap-8 justify-around">
            <x-virg.posts.category-buttons label="All Categories" />
            <x-virg.posts.category-buttons label="Guns" />
            <x-virg.posts.category-buttons label="Airsofts" />
            <x-virg.posts.category-buttons label="Ammunitions" />
            <x-virg.posts.category-buttons label="Accessories" />
            <x-virg.posts.category-buttons label="Others" />
        </div>
    </div>
    <flux:separator/>
    <div class="flex gap-3 mt-5">
        <div class="w-1/3 border-e-2 pe-2 space-y-5">
            <flux:heading>Search:</flux:heading>
            <flux:input type="text" placeholder="Search..." class="w-full rounded-md" />

            <div class="mb-5 space-y-4">
                <flux:heading>Post Type Filter:</flux:heading>
                <div class="flex gap-4 *:gap-x-2">
                    <flux:checkbox checked value="english" label="All" />
                    <flux:checkbox checked value="spanish" label="Sell" />
                    <flux:checkbox value="french" label="Buy" />
                </div>
            </div>

            <div class="space-y-5">
                <flux:heading>Price Range:</flux:heading>
                <div class="flex flex-col gap-2">
                    <flux:input type="number" placeholder="Min Price" class="w-full rounded-md" />
                    <flux:input type="number" placeholder="Max Price" class="w-full rounded-md" />
                </div>
            </div>

            <div>
                <flux:heading>Filter by Location:</flux:heading>

            </div>



        </div>
        <div class="w-2/3">

        @foreach ($this->posts as $post)
            <div class="border p-4 mb-4">
                <h2 class="text-xl font-bold">{{ $post->title }}</h2>
                <p>{{ $post->description }}</p>
            </div>
        @endforeach

        </div>
    </div>
    <div>

    </div>

</div>

1
