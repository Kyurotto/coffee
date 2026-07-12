<div wire:poll.15s class="space-y-6">

    {{-- Summary bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Total Orders</p>
            <p class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Today's Orders</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $todayOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Today Revenue</p>
            <p class="text-2xl font-extrabold text-green-600 mt-1">${{ $todayRevenue }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 text-center">
            <p class="text-xs text-gray-500 uppercase tracking-wider">All-Time Revenue</p>
            <p class="text-2xl font-extrabold text-purple-600 mt-1">${{ $totalRevenue }}</p>
        </div>
    </div>

    {{-- Order table --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-12 h-12 mb-3 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-sm font-medium">No orders recorded yet</p>
                <p class="text-xs mt-1 text-gray-300">Orders from cashiers will appear here</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-950 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Receipt</th>
                            <th class="px-6 py-4 text-left">Cashier</th>
                            <th class="px-6 py-4 text-left">Items</th>
                            <th class="px-6 py-4 text-left">Payment</th>
                            <th class="px-6 py-4 text-left">Time</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition">
                                <td class="px-6 py-3 font-mono text-xs font-bold text-gray-700 dark:text-gray-300">{{ $order->receipt_no }}</td>
                                <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ $order->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3 text-gray-500 text-xs max-w-xs truncate">
                                    {{ $order->items->map(fn($i) => $i->product_name . ' x' . $i->quantity)->implode(', ') }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                                        {{ $order->payment_method === 'physical'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $order->payment_method === 'physical' ? 'Cash' : 'Online' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs">{{ $order->created_at->format('h:i A') }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 capitalize">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-extrabold text-amber-600 dark:text-amber-400">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
