<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class ReportSummary extends Component
{
    public function render()
    {
        $totalOrders  = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $todayRevenue = Order::where('status', 'completed')
                             ->whereDate('created_at', today())
                             ->sum('total');
        $todayOrders  = Order::whereDate('created_at', today())->count();

        return view('livewire.report-summary', compact(
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders'
        ));
    }
}
