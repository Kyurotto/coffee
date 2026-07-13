<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view, branching on role.
     */
    public function index()
    {
        $milkteas = Product::where('category', 'milktea')->latest()->get();
        $snacks   = Product::where('category', 'snack')->latest()->get();

        // Pre-compute admin order stats to render server-side into the view.
        // The admin dashboard also polls /orders/summary via JS for live updates.
        $totalOrders  = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $todayRevenue = Order::where('status', 'completed')
                             ->whereDate('created_at', today())
                             ->sum('total');
        $todayOrders  = Order::whereDate('created_at', today())->count();

        $recentOrders = Order::with('user', 'items')
                             ->latest()
                             ->limit(10)
                             ->get();

        $paginatedProducts = Product::paginate(10);

        return view('dashboard', compact(
            'milkteas',
            'snacks',
            'totalOrders',
            'totalRevenue',
            'todayRevenue',
            'todayOrders',
            'recentOrders',
            'paginatedProducts'
        ));
    }
}
