<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>

            {{-- This one holds the dark and Light Logo of the App --}}
            <div class="flex justify-center">
                <a href="/">
                    <x-virg.logo />
                </a>
            </div>

            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>
        <flux:separator />

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('')" class="grid gap-1">
                <flux:sidebar.item icon="home" :href="route('home')" :current="request()->routeIs('home')"
                    wire:navigate>
                    {{ __('Home') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="signpost" :href="route('posts')" :current="request()->routeIs('posts')"
                    wire:navigate>
                    {{ __('Posts') }}
                </flux:sidebar.item>

                @auth
                    {{-- Routes for Admin --}}
                    @if (auth()->user()->status == 'immune' && auth()->user()->account_type == 'TFT_admin')
                        <flux:sidebar.item icon="layout-dashboard" :href="route('dashboard')"
                            :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>
                    @endif
                    <flux:sidebar.item icon="shopping-bag" :href="route('order')" :current="request()->routeIs('order')"
                        wire:navigate>
                        {{ __('Order') }}
                    </flux:sidebar.item>
                @endauth


                <flux:sidebar.item icon="badge-question-mark" :href="route('help')"
                    :current="request()->routeIs('help')" wire:navigate>
                    {{ __('Help') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        @persist('toast')
            <flux:toast position="top end" />
        @endpersist

        <flux:spacer />

        @auth
            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->first_name" />
        @endauth


        <flux:separator />
        <flux:switch x-data x-model="$flux.dark" label="Dark mode" />

    </flux:sidebar>


    @auth
        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    src="{{ auth()->user()->avatar_path ? url('storage/' . auth()->user()->avatar_path) : asset('blank_image.png') }}"
                                    :name="auth()->user()->first_name" :initials="auth()->user()->initials()" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->first_name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
    @endauth



    {{ $slot }}

    @fluxScripts
</body>

</html>
