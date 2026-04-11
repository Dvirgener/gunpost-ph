<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\posts\Post;
use Livewire\Attributes\Computed;

new #[Layout('layouts::guest')] class extends Component {
    #[Computed]
    public function posts()
    {
        return Post::where('status', '=', 'approved')->orderBy('approved_at', 'asc')->limit(6)->get();
    }
};
?>

<div class="h-full min-h-0 overflow-y-auto overflow-x-hidden px-4">
    {{-- <div class="relative w-full overflow-hidden dark:text-white py-2">
        <div class="flex flex-col">
            <div class="animate-marquee whitespace-nowrap text-lg w-full">
                <span class="mx-2">🚨 Buy. Sell. Trade. | 🔫 Post your firearm listings now on GunPost PH – The #1
                    Online
                    Gun Marketplace in the Philippines! 🇵🇭</span>
                <span class="mx-2">🚨 Buy. Sell. Trade. | 🔫 Post your firearm listings now on GunPost PH – The #1
                    Online
                    Gun Marketplace in the Philippines! 🇵🇭</span>
            </div>
        </div>
    </div> --}}
    {{-- <style>
        @keyframes marquee {
            0% {
                transform: translateX(10%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            min-width: 100%;
            animation: marquee 30s linear infinite;
        }
    </style> --}}


    {{-- Main Logo --}}
    <div class="w-full flex justify-center">
        <div class="w-200">
            <x-virg.logo />
        </div>
    </div>
    {{-- Main Logo --}}

    {{-- Beta State Callout --}}
    <div class="mx-5 my-5 text-sm">
        <flux:callout color="violet" class="dark:bg-violet-900/20">
            <x-slot name="icon">
                <flux:icon.exclamation-triangle />

            </x-slot>
            <flux:callout.heading>GunPost.ph is currently in its Beta stage.</flux:callout.heading>
            <flux:callout.text>
                <p class="text-justify mb-3 text-xs">
                    This means the platform is still under active development and may undergo frequent updates,
                    feature changes, or occasional downtime. Some functionalities may not yet be fully optimized or
                    available.
                </p>
                <p class="text-justify mb-3">
                    We genuinely appreciate your interest and support during this early phase. Your feedback,
                    suggestions, and bug reports are invaluable in helping us improve and shape GunPost.ph into the
                    best possible experience for our users.
                </p>
                <p class="text-justify">
                    If you encounter any issues or have ideas to share, please don’t hesitate to contact us — your
                    input makes a real difference.
                </p>
            </flux:callout.text>
            <div class="my-2">
                <flux:button variant="primary" icon="ticket" href="/help">Create a Ticket</flux:button>
            </div>

        </flux:callout>
    </div>
    {{-- Beta State Callout --}}

    {{-- Latest Postings --}}
    <h1 class="text-md font-bold text-center text-zinc-900 dark:text-white mb-4 uppercase">Latest Postings</h1>
    <flux:separator class="mb-4" />
    {{-- Post Cards --}}
    <div class="flex justify-center">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-6">

            @foreach ($this->posts as $post)
                <x-home.post-card :post="$post" />
            @endforeach



        </div>
    </div>
    <flux:separator class="my-4" />
    {{-- Latest Postings --}}
    <div class="mx-5 text-sm">
        <h1 class="font-bold text-center text-zinc-900 dark:text-white mb-4 italic">Buy and Sell Firearms
            Online –
            Welcome to GunPost PH</h1>
        <p class="text-center text-gray-600 dark:text-gray-300 mb-8 px-5">GunPost PH is the leading online gun
            posting platform in the Philippines, built for responsible firearm owners, collectors, and licensed
            dealers. Whether you're selling a personal handgun, looking to buy a rifle, or managing listings as a
            registered firearm business, GunPost PH offers a safe, compliant, and easy-to-use marketplace tailored
            to the local community. Our secure registration system and user-friendly tools help you list, browse,
            and connect—all in one trusted platform.</p>
    </div>
    <div class="mx-5 text-sm">
        <h1 class="font-bold text-center text-zinc-900 dark:text-white mb-4 italic">Post Legally. Trade
            Confidently. Personal and Business Accounts Welcome.</h1>
        <p class=" text-center text-gray-600 dark:text-gray-300 mb-8 px-5">We support both personal and corporate
            accounts, making it easy for individuals and firearm dealers to list and manage gun sales in compliance
            with Philippine law and PNP regulations. All postings are reviewed for accuracy and legal documentation
            to ensure a secure transaction environment. Join thousands of users who are changing the way firearms
            are bought and sold in the Philippines.</p>
    </div>
    <div
        class="flex flex-col md:flex-row items-center justify-center gap-2 text-lg text-center text-gray-600 dark:text-gray-300 mb-8 ">
        <span class="text-md hidden md:block">👉</span>
        <flux:button variant="primary" icon="user-plus" href="/register">Register Now</flux:button>
        <span class="mx-3 text-sm">and start posting your firearms legally and securely.</span>
    </div>
</div>
