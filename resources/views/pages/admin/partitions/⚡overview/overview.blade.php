<div class="w-full">
    <div
        class="py-5 px-4 w-full border border-gray-400/50 dark:border-none dark:shadow-white/50 rounded-md shadow dark:bg-gray-700 mb-3">
        <div class="flex justify-start items-center gap-3 mb-5">
            <flux:heading size="xl" class="font-mono text-start my-2 font-bold">USERS</flux:heading>
            <flux:button icon="square-arrow-right" variant="subtle"
                class="hover:cursor-pointer hover:scale-105 transition-all duration-150"
                href="{{ route('admin.users') }}" />
        </div>

        <div class="w-full grid grid-cols-2 md:flex gap-3 justify-around">
            <x-virg.admin.num-card title="All" :number="$allUsersCount" />
            <x-virg.admin.num-card title="KYC Verified" :number="$kycVerifiedUsersCount" class="text-green-500" />
            <x-virg.admin.num-card title="Pending KYC" :number="$pendingKycUsersCount" />
            <x-virg.admin.num-card title="KYC For Approval" :number="$kycForApproval" class="text-purple-600" />
            <x-virg.admin.num-card title="Flagged" :number="$flaggedUsersCount" class="text-red-500" />
        </div>
    </div>

    <div
        class="py-5 px-4 w-full border border-gray-400/50 dark:border-none dark:shadow-white/50 rounded-md shadow dark:bg-gray-700 mb-3">
        <div class="flex justify-start items-center gap-3 mb-5">
            <flux:heading size="xl" class="font-mono text-start my-2 font-bold">POSTINGS</flux:heading>
            <flux:button icon="square-arrow-right" variant="subtle"
                class="hover:cursor-pointer hover:scale-105 transition-all duration-150"
                href="{{ route('admin.posts') }}" />
        </div>

        <div class="w-full grid grid-cols-2 md:flex gap-3 justify-around">
            <x-virg.admin.num-card title="All" :number="$allPostsCount" />
            <x-virg.admin.num-card title="Published" :number="$publishedPostsCount" class="text-green-500" />
            <x-virg.admin.num-card title="Drafts" :number="$pendingPostsCount" class="text-purple-600" />
            <x-virg.admin.num-card title="Expired" :number="$expiredPostsCount" />
            <x-virg.admin.num-card title="Archived" :number="$archivedPostsCount" />
            <x-virg.admin.num-card title="Rejected" :number="$rejectedPostsCount" class="text-red-500" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 space-y-3 md:space-y-0 md:gap-3">
        <div
            class="py-5 px-4 w-full border border-gray-400/50 dark:border-none dark:shadow-white/50 rounded-md shadow dark:bg-gray-700">
            <div class="flex justify-start items-center gap-3 mb-5">
                <flux:heading size="xl" class="font-mono text-start my-2 font-bold">CREDIT ORDERS
                </flux:heading>
                <flux:button icon="square-arrow-right" variant="subtle"
                    class="hover:cursor-pointer hover:scale-105 transition-all duration-150"
                    href="{{ route('admin.orders') }}" />
            </div>

            <div class="w-full grid grid-cols-2 md:flex gap-3 justify-around">
                <x-virg.admin.num-card title="Completed" :number="$confirmedCreditOrdersCount" class="text-green-500" />
                <x-virg.admin.num-card title="Pending" :number="$pendingCreditOrdersCount" class="text-purple-600" />

            </div>
        </div>

        <div
            class="py-5 px-4 w-full border border-gray-400/50 dark:border-none dark:shadow-white/50 rounded-md shadow dark:bg-gray-700 col-span-2">
            <div class="flex justify-start items-center gap-3 mb-5">
                <flux:heading size="xl" class="font-mono text-start my-2 font-bold">HELP TICKETS
                </flux:heading>
                <flux:button icon="square-arrow-right" variant="subtle"
                    class="hover:cursor-pointer hover:scale-105 transition-all duration-150"
                    href="{{ route('admin.tickets') }}" />
            </div>

            <div class="w-full grid grid-cols-2 md:flex gap-3 justify-around">
                <x-virg.admin.num-card title="On Going" :number="$openTicketsCount" />
                <x-virg.admin.num-card title="New" :number="$pendingTicketsCount" class="text-purple-600" />
                <x-virg.admin.num-card title="Closed" :number="$closedTicketsCount" class="text-green-500" />

            </div>
        </div>
    </div>
</div>
