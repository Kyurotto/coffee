<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderHistory extends Component
{
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
            ->limit(50)
            ->get();

        return view('livewire.order-history', compact(
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders',
            'orders'
        ));
    }
}
