<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Store a new order placed by an employee (cashier).
     * Called via AJAX from the POS employee view.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;

            // Build items with database-sourced prices (never trust client prices)
            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineSubtotal = $product->price * $item['quantity'];
                $subtotal += $lineSubtotal;

                $orderItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $lineSubtotal,
                ];
            }

            $tax   = round($subtotal * 0.10, 2);
            $total = round($subtotal + $tax, 2);

            // Create the parent order
            $order = Order::create([
                'user_id'    => Auth::id(),
                'receipt_no' => 'REC-' . strtoupper(Str::random(8)),
                'subtotal'   => $subtotal,
                'tax'        => $tax,
                'total'      => $total,
                'status'     => 'completed',
            ]);

            // Create each order item
            foreach ($orderItems as $itemData) {
                $order->items()->create($itemData);
            }

            DB::commit();

            // Return the saved order with its items for the receipt modal
            return response()->json([
                'success'    => true,
                'receipt_no' => $order->receipt_no,
                'subtotal'   => number_format($order->subtotal, 2),
                'tax'        => number_format($order->tax, 2),
                'total'      => number_format($order->total, 2),
                'items'      => $order->items->map(fn($i) => [
                    'name'     => $i->product_name,
                    'quantity' => $i->quantity,
                    'price'    => number_format($i->unit_price, 2),
                    'subtotal' => number_format($i->subtotal, 2),
                ]),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a JSON summary of all orders (admin view).
     * Used by the admin dashboard to poll live totals across devices.
     */
    public function summary()
    {
        $totalOrders  = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $todayRevenue = Order::where('status', 'completed')
                             ->whereDate('created_at', today())
                             ->sum('total');
        $todayOrders  = Order::whereDate('created_at', today())->count();

        // Recent 10 orders for the live feed
        $recentOrders = Order::with('user', 'items')
                             ->latest()
                             ->limit(10)
                             ->get()
                             ->map(fn($o) => [
                                 'id'         => $o->id,
                                 'receipt_no' => $o->receipt_no,
                                 'cashier'    => $o->user->name ?? 'Unknown',
                                 'total'      => number_format($o->total, 2),
                                 'status'     => $o->status,
                                 'time'       => $o->created_at->format('h:i A'),
                                 'items'      => $o->items->map(fn($i) => [
                                     'name'     => $i->product_name,
                                     'quantity' => $i->quantity,
                                 ]),
                             ]);

        return response()->json([
            'total_orders'  => $totalOrders,
            'total_revenue' => number_format($totalRevenue, 2),
            'today_revenue' => number_format($todayRevenue, 2),
            'today_orders'  => $todayOrders,
            'recent_orders' => $recentOrders,
        ]);
    }
}
