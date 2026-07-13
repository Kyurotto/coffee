<div wire:poll.15s class="space-y-6">

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Products (static - product count doesn't need live update) --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Products</p>
                <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white mt-1">{{ $totalProducts }}</h3>
            </div>
            <div class="bg-blue-500/10 p-3 rounded-xl text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        {{-- Today's Orders --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Today's Orders</p>
                <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $todayOrders }}</h3>
            </div>
            <div class="bg-amber-500/10 p-3 rounded-xl text-amber-600 dark:text-amber-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        {{-- Today's Revenue --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Today's Revenue</p>
                <h3 class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-1">${{ $todayRevenue }}</h3>
            </div>
            <div class="bg-green-500/10 p-3 rounded-xl text-green-600 dark:text-green-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16V5"/></svg>
            </div>
        </div>

        {{-- All-Time Revenue --}}
        <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">All-Time Revenue</p>
                <h3 class="text-3xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">${{ $totalRevenue }}</h3>
            </div>
            <div class="bg-purple-500/10 p-3 rounded-xl text-purple-600 dark:text-purple-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

    </div>

    {{-- Recent orders live feed + quick summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Live recent orders --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 lg:col-span-2 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                <span class="flex items-center space-x-1.5 text-[10px] text-green-600 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                    <span>Live</span>
                </span>
            </div>

            @if($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-10 h-10 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm">No orders yet today</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($recentOrders as $order)
                        <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/60 dark:hover:bg-gray-800/30 transition">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-gray-800 dark:text-white">{{ $order['receipt_no'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $order['status'] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Cashier: <span class="font-medium text-gray-600 dark:text-gray-300">{{ $order['cashier'] }}</span>
                                    &bull; {{ $order['time'] }}
                                </p>
                                <p class="text-[11px] text-gray-400 truncate">
                                    {{ collect($order['items'])->map(fn($i) => $i['name'] . ' x' . $i['quantity'])->implode(', ') }}
                                </p>
                            </div>
                            <span class="ml-4 text-sm font-extrabold text-amber-600 dark:text-amber-400 shrink-0">${{ $order['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Quick summary sidebar --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800/80 flex flex-col space-y-5">
            <h3 class="font-bold text-gray-800 dark:text-white">Quick Summary</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">All-time orders</span>
                    <span class="font-bold text-gray-800 dark:text-white">{{ $totalOrders }} orders</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Today's orders</span>
                    <span class="font-bold text-amber-600">{{ $todayOrders }} orders</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Today's revenue</span>
                    <span class="font-bold text-green-600">${{ $todayRevenue }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">All-time revenue</span>
                    <span class="font-bold text-purple-600">${{ $totalRevenue }}</span>
                </div>
            </div>
            <div class="border-t border-dashed border-gray-200 dark:border-gray-800 pt-4">
                <button @click="activeTab = 'orders'" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl text-xs transition shadow-sm">
                    View All Orders →
                </button>
            </div>
        </div>

    </div>

</div>
