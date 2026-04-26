<?php

use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    public $order;

    public $photo;

    public $newCredits;

    #[On('getOrder')]
    public function getOrder(Order $order)
    {
        $this->order = $order;
        $this->calculateCredits();
    }

    public function removePhoto()
    {
        $this->photo = null;
    }

    private function calculateCredits()
    {
        $this->newCredits = $this->order->user->post_credits + $this->order->credits;
    }

    public function confirmPayment()
    {
        $this->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 10MB Max
        ]);

        $photoPath = $this->photo ? $this->photo->store('order/proof', 'public') : null;

        // Process payment confirmation logic here
        // e.g., update order status, add credits to user account, save photos, etc.

        // For example:
        $this->order->image_path = $photoPath;
        $this->order->confirmed_by_id = auth()->id();
        $this->order->confirmed_at = now();
        $this->order->status = 'paid';
        $this->order->save();

        $user = $this->order->user;
        $user->post_credits += $this->order->credits;
        $user->save();

        // Reset state
        $this->photo = null;
        $this->order = null;
        $this->newCredits = null;

        // Optionally, you can emit an event or flash a message to notify success
        Flux::toast(text: 'Credit Added to User.', variant: 'success');

        $this->dispatch('orderConfirmed');
    }
};
?>

<div>
    @if ($order)
        <div class="space-y-6">

            <div>
                <flux:heading size="lg">Confirm Payment</flux:heading>
                <flux:text class="mt-2">Confirm payment and add purchased credits to User's Credit Balance.</flux:text>
            </div>

            <div class="flex flex-col space-y-4">
                <flux:input label="Reqeusted by" disabled value="{{ $order->user->first_name }}" />
                <flux:input label="Package" disabled value="{{ $order->package }}" />
                <flux:input label="Quantity" type="number" disabled value="{{ $order->quantity }}" />
                <flux:input label="Total Price" type="number" disabled value="{{ $order->amount }}" />
                <flux:input label="Payment Method" type="text" disabled value="{{ $order->payment_method }}" />
            </div>
            <flux:separator />


            @if ($order->status == 'pending')
                <div class="flex flex-col space-y-4">

                    <flux:input label="Credits" type="email" disabled value="{{ $order->user->post_credits }}" />
                    <flux:input label="Total Credits" type="number" disabled value="{{ $newCredits }}" />

                </div>
                <form action="" enctype="multipart/form-data" wire:submit="confirmPayment">
                    <flux:file-upload wire:model="photo" label="Proof of Payment">
                        <flux:file-upload.dropzone heading="Drop files or click to browse"
                            text="JPG, PNG, GIF up to 10MB" with-progress inline />
                    </flux:file-upload>

                    <div class="my-4 flex flex-col gap-2">
                        @if ($photo)
                            <flux:file-item heading="" :image="$photo->temporaryUrl()">
                                <x-slot name="actions">
                                    <flux:file-item.remove wire:click="removePhoto()" />
                                </x-slot>
                            </flux:file-item>
                        @endif
                    </div>

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary">Confirm Payment</flux:button>
                    </div>

                </form>
            @else
                <flux:badge color="green" size="sm">Payment Confirmed</flux:badge>
                <flux:input label="Confirmed By" disabled value="{{ $order->confirmer->first_name }}" />
                <flux:input label="Confirmed At" disabled value="{{ $order->confirmed_at }}" />
                <flux:text>Proof of Payment:</flux:text>
                @if ($order->image_path)
                    <img src="{{ url('storage/' . $order->image_path) }}" alt="Proof of Payment"
                        class="w-full h-auto rounded">
                @else
                    <flux:text>No proof of payment uploaded.</flux:text>
                @endif
            @endif




        </div>

    @endif
</div>
