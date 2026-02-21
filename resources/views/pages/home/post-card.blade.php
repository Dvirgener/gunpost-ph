@props(
    [

    ]
)


<div class="w-40 h-full bg-neutral-900 dark:border-white flex justify-center items-center cursor-pointer hover:scale-105 transition-transform duration-300 ease-in-out hover:cursor-pointer ">
    <div>
    <flux:dropdown hover position="top" gap="20" align="center" class="w-full text-center ">
            <button type="button" class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-stone-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <img src="https://picsum.photos/seed/picsum/200/100" alt="" class="w-full h-35 hover:cursor-pointer text-center">
            </button>
            <flux:popover>
                <div class="space-y-3 text-center">
                    <div class="flex justify-center">
                        <img src="https://picsum.photos/800/60" class="w-14 h-14 border shadow outline" alt="">
                    </div>

                    <div>
                        <flux:heading size="lg"></flux:heading>
                        <div class="flex flex-col  items-center gap-2">
                            <flux:text size="md" class="hover:font-bold">Jayfrill Virgener Naya</flux:text>
                            <flux:badge color="green" size="sm">2 Active postings</flux:badge>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">

                    </div>
                    <div class="flex gap-2">
                        <flux:button variant="primary" href=""
                            size="sm" icon="chat-bubble-left-right" icon:class="opacity-45" class="flex-1 hover:cursor-pointer">Send
                            Inquiry</flux:button>
                    </div>
                </div>
            </flux:popover>
    </flux:dropdown>
    </div>

</div>
