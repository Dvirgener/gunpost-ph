<div class="flex flex-col h-full">
    <div class="mx-auto max-w-7xl p-4">
        <h1 class="font-bold text-2xl">ORDERS</h1>



    </div>
    <div class="flex-1 h-min-0 overflow-y-scroll px-2 pb-5">
        <form action="" wire:submit="placeOrder">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 my-10 w-full">

                <div class="w-full flex justify-center md:justify-start">
                    <flux:radio.group label="Packages" variant="cards"
                        class="grid grid-cols-2 md:flex md:flex-col w-70! md:w-full! "
                        wire:model.live="selectedPackage">
                        @foreach ($packages as $package)
                            <flux:radio value="{{ $package['name'] }}" label="{{ $package['name'] }}"
                                description="{{ $package['description'] }}" class="w-full!" />
                        @endforeach
                    </flux:radio.group>
                </div>

                <div class="px-3 md:pt-9 col-span-2">
                    @if ($displayPackage)
                        <div
                            class="mt-4 p-4 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $displayPackage['name'] }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $displayPackage['fullDescription'] }}
                            </p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Price: PhP
                                {{ number_format($displayPackage['price'], 2) }}
                            </p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 space-x-2 w-full mt-4">
                        <flux:input type="number" label="Quantity" wire:model.live="quantity" />
                        <flux:select label="Payment Option" wire:model="paymentOption">
                            <flux:select.option value="cash">Cash</flux:select.option>
                            <flux:select.option value="maya">Maya</flux:select.option>
                            <flux:select.option value="gcash">G Cash</flux:select.option>
                            <flux:select.option value="bank">Bank Transfer</flux:select.option>

                        </flux:select>

                    </div>
                    <div class="grid grid-cols-2 space-x-2 w-full my-4">

                        <flux:input type="number" label="Package Price" disabled wire:model="price" />
                        <flux:input type="number" label="Total Price" disabled wire:model="totalPrice" />
                    </div>
                    <div>
                        <flux:button variant="primary" class="w-full hover:cursor-pointer" type="submit">Place Order
                        </flux:button>
                    </div>
                </div>

            </div>

        </form>
        <div class="my-5 px-8">
            <p class="font-semibold text-sm text-red-500 text-center underline ">For Ads more than 50, please Contact
                our
                administrators...</p>
        </div>

        <div>
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Order History</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">You can view all your orders here.</p>
            </div>
            <div class="mb-3">
                <flux:pagination :paginator="$this->orders" />
            </div>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Package ID</flux:table.column>
                    <flux:table.column>Package</flux:table.column>
                    <flux:table.column>Quantity</flux:table.column>
                    <flux:table.column>Total Credits</flux:table.column>
                    <flux:table.column>Date Placed</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->orders as $order)
                        <flux:table.row>
                            <flux:table.cell variant="strong">{{ $order->ticket_id }}</flux:table.cell>
                            <flux:table.cell variant="strong">{{ $order->package }}</flux:table.cell>
                            <flux:table.cell>{{ $order->quantity }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $order->credits }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-virg.admin.virg-date :date="$order->created_at" />
                            </flux:table.cell>
                            <flux:table.cell>
                                @switch($order->status)
                                    @case('pending')
                                        <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                    @break

                                    @case('paid')
                                        <flux:badge color="green" size="sm">Paid</flux:badge>
                                    @break
                                @endswitch
                            </flux:table.cell>
                            <flux:table.cell variant="strong">
                                <x-virg.admin.virg-amount :amount="$order->amount" />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach


                </flux:table.rows>
            </flux:table>
        </div>
    </div>

</div>
