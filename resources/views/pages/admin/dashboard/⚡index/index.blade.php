<div class="p-4">
    <div class="flex justify-between mb-5">
        <h1 class="font-bold text-2xl">ADMIN DASHBOARD</h1>
    </div>
    <div>
        <flux:heading size="lg" class="font-mono text-start my-2 font-bold">OVERVIEW</flux:heading>
    </div>
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-4 w-full">
            <x-virg.admin.number-card label="Total Users" :numbers="$userCount" />
            <x-virg.admin.number-card label="Total Posts" :numbers="$postCount" />
            <x-virg.admin.number-card label="Total Orders" :numbers="$orderCount" />
            <x-virg.admin.number-card label="Total Tickets" :numbers="$ticketCount" />

        </div>
        <div>
            <flux:heading size="lg" class="font-mono text-start my-2 font-bold">FOR ACTION</flux:heading>
        </div>
        <div class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-4 w-full">
                <x-virg.admin.number-card label="Pending Users" :numbers="$pendingUserCount" link="{{ route('admin.users') }}" />
                <x-virg.admin.number-card label="Drafts" :numbers="$pendingPostCount" link="{{ route('admin.posts') }}" />
                <x-virg.admin.number-card label="Pending Orders" :numbers="$pendingOrderCount" link="{{ route('admin.orders') }}" />
                <x-virg.admin.number-card label="Pending Tickets" :numbers="$pendingTicketCount"
                    link="{{ route('admin.tickets') }}" />
            </div>
        </div>
    </div>
