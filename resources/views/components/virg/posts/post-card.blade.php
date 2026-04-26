@props([
    'post' => null,
])

<div class="outline outline-gray-200 dark:outline-gray-600 rounded-md h-max">
    <div class="border-b p-2 flex justify-between gap-5 items-center">
        <a href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
            class="hover:text-purple-500">
            <h3 class="font-bold text-sm line-clamp-1">{{ $post->title }}</h3>
        </a>
        <x-virg.posts.category-icon :cat="$post->category" />

    </div>
    <div class="flex gap-2 pe-1 ">
        @if ($post->p_1)
            <img src="{{ $post->p_1 }}" alt="" class="w-35 h-35 border">
        @else
            <flux:icon icon="photo" class="w-35 h-35 text-gray-400" />
        @endif


        <div class="h-35 space-y-2 py-2 flex flex-col justify-between">

            <div class="flex justify-between gap-2">
                @if ($post->listing_type == 'buy')
                    <flux:badge size="sm" color="violet" class="px-2!">{{ $post->listing_type }}</flux:badge>
                @else
                    <flux:badge size="sm" color="green" class="px-2!">{{ $post->listing_type }}</flux:badge>
                @endif

                <a href="{{ route('profile.visit',['user' => $post->user]) }}">
                    <flux:tooltip content="{{'Visit '. $post->user->first_name . ' Profile' }}">
                        <flux:avatar size="xs" src="{{url($post->user->avatar_path ? $post->user->avatar_path : asset('blank_image.png'))}}" />
                    </flux:tooltip>
                </a>

            </div>
            <p class="text-xs line-clamp-3 text-wrap text-ellipsis">{{ $post->description }}</p>

            <div class="text-xs">
                <span class="font-bold">Price: </span>
                <span>{{ 'P' . number_format((float) $post->price, 2, '.', ',') }}</span>
            </div>

        </div>
    </div>

</div>
