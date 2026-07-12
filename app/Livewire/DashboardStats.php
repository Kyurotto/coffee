<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalRevenue  = number_format(
            Order::where('status', 'completed')->sum('total'), 2
        );
        $todayRevenue = number_format(
            Order::where('status', 'completed')
                 ->whereDate('created_at', today())
                 ->sum('total'),
            2
        );
        $todayOrders = Order::whereDate('created_at', today())->count();

        $recentOrders = Order::with('user', 'items')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($o) => [
                'id'         => $o->id,
                'receipt_no' => $o->receipt_no,
                'cashier'    => $o->user->name ?? 'Unknown',
                'total'      => number_format($o->total, 2),
                'status'     => $o->status,
                'time'       => $o->created_at->format('h:i A'),
                'items'      => $o->items->map(fn ($i) => [
                    'name'     => $i->product_name,
                    'quantity' => $i->quantity,
                ])->toArray(),
            ]);

        // Dispatch a browser event so Alpine.js sidebar badge + Reports tab stay in sync
        $this->dispatch('stats-refreshed',
            totalOrders:  $totalOrders,
            totalRevenue: $totalRevenue,
            todayRevenue: $todayRevenue,
            todayOrders:  $todayOrders,
        );

        return view('livewire.dashboard-stats', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders',
            'recentOrders'
        ));
    }
}
