<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $totalOrders  = Order::count();
        $totalRevenue = number_format(
            Order::where('status', 'completed')->sum('total'), 2
        );
        $todayRevenue = number_format(
            Order::where('status', 'completed')
                 ->whereDate('created_at', today())
                 ->sum('total'),
            2
        );
        $todayOrders = Order::whereDate('created_at', today())->count();

        $orders = Order::with('user', 'items')
            ->latest()
            ->paginate(10);

        return view('livewire.order-history', compact(
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders',
            'orders'
        ));
    }
}
