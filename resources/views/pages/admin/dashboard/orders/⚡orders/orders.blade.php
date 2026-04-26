<div>
    <div class="flex justify-between">
        <flux:heading size="lg" class="font-mono text-start my-2 font-bold">CREDIT ORDERS</flux:heading>
        <flux:button color="red" variant="primary" class="hover:cursor-pointer" onclick="window.history.back()">Back
        </flux:button>
    </div>
    <div class="w-full">
        <div class="grid grid-cols-4 gap-4 my-4 w-full">

            <button wire:click="updateOrderFilter('all')"
                class="border rounded-md {{ $orderFilter == 'all' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="All Orders" numbers="{{ $allOrdersCount }}" />
            </button>
            <button wire:click="updateOrderFilter('confirmed')"
                class="border rounded-md {{ $orderFilter == 'confirmed' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Confirmed" numbers="{{ $confirmedOrdersCount }}" />
            </button>
            <button wire:click="updateOrderFilter('pending')"
                class="border rounded-md {{ $orderFilter == 'pending' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Pending" numbers="{{ $pendingOrdersCount }}" />
            </button>
            <button wire:click="updateOrderFilter('cancelled')"
                class="border rounded-md {{ $orderFilter == 'cancelled' ? 'border-amber-500' : '' }}">
                <x-virg.admin.number-card label="Cancelled" numbers="{{ $cancelledOrdersCount }}" />
            </button>

        </div>
    </div>
    <div>
        <div>
            <flux:pagination :paginator="$this->orders" />
        </div>
    </div>
    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Package ID</flux:table.column>
                <flux:table.column>Owner</flux:table.column>
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
                        <flux:table.cell variant="strong">

                            <flux:button wire:click="confirmOrder({{ $order->id }})" color="blue"
                                class="hover:cursor-pointer">
                                {{ $order->ticket_id }}
                            </flux:button>

                        </flux:table.cell>
                        <flux:table.cell variant="strong">
                            <flux:avatar
                                src="{{ $order->user->avatar_path ? url($order->user->avatar_path) : asset('blank_image.png') }}" />
                        </flux:table.cell>
                        <flux:table.cell variant="strong">

                            {{ $order->package }}


                        </flux:table.cell>
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
    <flux:modal name="confirm-order" variant="flyout">
        <livewire:pages::admin.dashboard.orders.confirm />
    </flux:modal>
</div>
