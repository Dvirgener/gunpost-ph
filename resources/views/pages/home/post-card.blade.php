@props([
    'post' => null,
])

@php
    $postCount = $post->user->posts->count();
@endphp
<div
    class="w-40 h-full bg-neutral-900 dark:border-white flex justify-center items-center cursor-pointer hover:scale-105 transition-transform duration-300 ease-in-out hover:cursor-pointer ">
    <div>
        <flux:dropdown hover position="top" gap="20" align="center" class="w-full text-center ">
            <button type="button"
                class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-stone-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <img src="{{ url($post->p_1) }}" alt="" class="w-full h-35 hover:cursor-pointer text-center">
            </button>
            <flux:popover>
                <div class="space-y-3 text-center">
                    <div class="flex justify-center">
                        <img src="{{ url($post->user->avatar_path ? $post->user->avatar_path : asset('blank_image.png')) }}"
                            class="w-14 h-14 border shadow outline" alt="">
                    </div>

                    <div>
                        <flux:heading size="lg"></flux:heading>
                        <div class="flex flex-col  items-center gap-2">
                            <flux:text size="md" class="hover:font-bold">{{ $post->user->fullName() }}</flux:text>
                            <flux:badge color="green" size="sm">{{ $postCount }} Active postings</flux:badge>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">

                    </div>
                </div>
            </flux:popover>
        </flux:dropdown>
    </div>

</div>
