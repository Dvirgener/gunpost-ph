<?php


use Livewire\Component;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Order;

new class extends Component
{
    use WithPagination;
    public $orderFilter = 'pending';

    public $allOrdersCount, $confirmedOrdersCount, $pendingOrdersCount, $cancelledOrdersCount;

    #[On('refreshList')]
    public function mount()
    {
        $this->allOrdersCount = Order::count();
        $this->confirmedOrdersCount = Order::where('status', 'confirmed')->count();
        $this->pendingOrdersCount = Order::where('status', 'pending')->count();
        $this->cancelledOrdersCount = Order::where('status', 'cancelled')->count();
    }

    public function updateOrderFilter($filter)
    {
        $this->orderFilter = $filter;
    }

    public function confirmOrder($order)
    {
        $this->dispatch('getOrder', $order);
        Flux::modal('confirm-order')->show();
    }

    #[On('orderConfirmed')]
    public function orderConfirmed()
    {
        $this->orders();
        $this->dispatch('refreshList');
        Flux::modal('confirm-order')->close();
    }

    #[Computed()]
    public function orders()
    {
        return Order::when($this->orderFilter != 'all', function ($query) {
            $query->where('status', $this->orderFilter);
        })->latest()->paginate(5);
    }
};
