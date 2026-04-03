<?php

use Livewire\Component;
use App\Models\Order;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component
{

use WithPagination;

    public $packages = [

        'Single' =>
            [
                'name' => 'Single',
                'price' => 750,
                'credits' => 1,
                'description' => 'Get 1 credit to use on posting ads.',
                'fullDescription' => 'This package is perfect for users who want to post a single ad. You will receive 1 credit that can be used to post one ad on the platform. This is a great option for those who are new to the platform or only need to post an ad occasionally. Each ad will last for 2 months.',
                'sign' => 'SG'
            ],
        'Basic' =>
            [
                'name' => 'Basic',
                'price' => 5000,
                'credits' => 10,
                'description' => 'Get 10 credits to use on posting ads.',
                'fullDescription' => 'This package is ideal for users who want to post multiple ads. You will receive 10 credits that can be used to post up to 10 ads on the platform. This package offers a good balance between cost and the number of ads you can post, making it suitable for regular users. Each ad will last for 2 months.',
                'sign' => 'BSC'
            ],
        'Standard' =>
            [
                'name' => 'Standard',
                'price' => 10000,
                'credits' => 25,
                'description' => 'Get 25 credits to use on posting ads.',
                'fullDescription' => 'This package is designed for users who need to post a significant number of ads. You will receive 25 credits that can be used to post up to 25 ads on the platform. This package is perfect for businesses or individuals who frequently post ads and want to maximize their reach on the platform. Each ad will last for 2 months.',
                'sign' => 'STD'
            ],
        'Premium' =>
            [
                'name' => 'Premium',
                'price' => 15000,
                'credits' => 50,
                'description' => 'Get 50 credits to use on posting ads..',
                'fullDescription' => 'This package is the best value for users who need to post a large number of ads. You will receive 50 credits that can be used to post up to 50 ads on the platform. This package is ideal for businesses or individuals who rely heavily on posting ads and want to take full advantage of the platform\'s features.  Each ad will last for 2 months.',
                'sign' => 'PRM'
            ],

    ];

    public $selectedPackage = "Single";
    public $displayPackage;
    public $quantity = 1;
    public $paymentOption = 'cash';
    public $price;
    public $totalPrice;
    public $totalCredits;
    public function mount()
    {
        $this->displayPackage = $this->packages[$this->selectedPackage];
        $this->price = $this->displayPackage['price'];
        $this->totalPrice = $this->price * $this->quantity;
        $this->totalCredits = $this->displayPackage['credits'];

    }

    public function updatedSelectedPackage($value)
    {
        $this->displayPackage = $this->packages[$value];
        $this->price = $this->displayPackage['price'];
        $this->totalPrice = $this->price * $this->quantity;
        $this->totalCredits = $this->displayPackage['credits'] * $this->quantity;
    }

    public function updatedQuantity($value)
    {
        $this->totalPrice = $this->price * $value;
        $this->totalCredits = $this->displayPackage['credits'] * $value;
    }

    public function placeOrder()
    {
        $order = Order::create([
            'package' => $this->displayPackage['name'],
            'quantity' => $this->quantity,
            'credits' => $this->totalCredits,
            'payment_method' => $this->paymentOption,
            'user_id' => auth()->user()->id,
            'amount' => $this->totalPrice
        ]);

        $order->ticket_id = $this->displayPackage['sign'] . str_pad($order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        $this->quantity = 1;
        $this->selectedPackage = "Single";
        $this->paymentOption = null;
        $this->updatedSelectedPackage($this->selectedPackage);

        Flux::toast(
            heading: 'Order Placed!',
            text: 'Thank you for placing an order. An admin will take care of the Credit.',
            variant: 'success',
        );

    }

    #[Computed()]
    public function orders()
    {
        return Order::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(5);
    }
};
