<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 pb-7">
            <flux:sidebar.header>

                {{-- This one holds the dark and Light Logo of the App --}}
                <div class="flex justify-center">
                    <a href="/">
                    <x-virg.logo/>
                    </a>
                </div>

                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>
            <flux:separator />

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('')" class="grid gap-2">

                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('home')" wire:navigate>
                        {{ __('Home') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="signpost" :href="route('dashboard')" :current="request()->routeIs('posts')" wire:navigate>
                        {{ __('Posts') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="badge-question-mark" :href="route('dashboard')" :current="request()->routeIs('posts')" wire:navigate>
                        {{ __('Help') }}
                    </flux:sidebar.item>

                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />


            {{-- This part is the Log in / Register Navigation Buttons --}}

            <flux:sidebar.nav>
                <flux:sidebar.item icon="user-key" href="{{ route('login') }}">
                    {{ __('Log in') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="user-round-plus" href="{{ route('register') }}">
                    {{ __('Register') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
            <flux:separator />
            <flux:switch x-data x-model="$flux.dark" label="Dark mode"  />


        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
