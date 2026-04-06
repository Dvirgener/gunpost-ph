        @guest
            <flux:callout color="red" class="dark:bg-violet-900/20 mb-5">
                <x-slot name="bell">
                    <flux:icon.exclamation-triangle />

                </x-slot>
                <flux:callout.heading>Welcome Guest</flux:callout.heading>
                <flux:callout.text>
                    <p class="text-justify mb-3 text-xs">
                        You are currently viewing as a guest. To access all features, details, and create posts, please log
                        in
                        or register
                        an account.
                    </p>
                </flux:callout.text>
                <div class="my-2 flex gap-2">

                    <flux:button variant="primary" size="sm" icon="user" href="/login">Login</flux:button>
                    <flux:button variant="primary" size="sm" icon="user-plus" href="/register">Register</flux:button>
                </div>

            </flux:callout>
        @endguest
