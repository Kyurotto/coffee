<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class ReportSummary extends Component
{
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }


    public function getCategoryRevenueData()
    {
        return \DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereMonth('orders.created_at', $this->selectedMonth)
            ->whereYear('orders.created_at', $this->selectedYear)
            ->select('products.category', \DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('products.category')
            ->get()
            ->mapWithKeys(fn($item) => [$item->category => (float)$item->total])
            ->toArray();
    }

    public function render()
    {
        $totalOrders  = Order::whereMonth('created_at', $this->selectedMonth)
                             ->whereYear('created_at', $this->selectedYear)
                             ->count();

        $totalRevenue = Order::where('status', 'completed')
                             ->whereMonth('created_at', $this->selectedMonth)
                             ->whereYear('created_at', $this->selectedYear)
                             ->sum('total');

        $todayRevenue = Order::where('status', 'completed')
                             ->whereDate('created_at', today())
                             ->sum('total');
        $todayOrders  = Order::whereDate('created_at', today())->count();

        $categoryRevenue = $this->getCategoryRevenueData();

        return view('livewire.report-summary', compact(
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders',
            'categoryRevenue'
        ));
    }
}
