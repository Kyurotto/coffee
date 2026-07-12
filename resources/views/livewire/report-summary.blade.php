<div wire:poll.15s class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-gray-800 dark:text-white mb-5">Revenue Summary</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500 font-medium">Today's Revenue</span>
                    <span class="font-bold text-gray-800 dark:text-white">${{ number_format($todayRevenue, 2) }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 h-3 rounded-full overflow-hidden">
                    <div class="bg-green-500 h-full rounded-full transition-all" style="width: {{ $totalRevenue > 0 ? min(100, ($todayRevenue / max($totalRevenue, 1)) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500 font-medium">All-Time Revenue</span>
                    <span class="font-bold text-gray-800 dark:text-white">${{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 h-3 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col justify-between">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">Order Totals</h3>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            <div class="py-2.5 flex justify-between text-sm">
                <span class="text-gray-500">All-Time Orders</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $totalOrders }} orders</span>
            </div>
            <div class="py-2.5 flex justify-between text-sm">
                <span class="text-gray-500">Today's Orders</span>
                <span class="font-semibold text-amber-600">{{ $todayOrders }} orders</span>
            </div>
            <div class="py-2.5 flex justify-between text-sm">
                <span class="text-gray-500">All-Time Revenue</span>
                <span class="font-semibold text-purple-600">${{ number_format($totalRevenue, 2) }}</span>
            </div>
            <div class="py-2.5 flex justify-between text-sm">
                <span class="text-gray-500">Today's Revenue</span>
                <span class="font-semibold text-green-600">${{ number_format($todayRevenue, 2) }}</span>
            </div>
        </div>
    </div>
</div>
